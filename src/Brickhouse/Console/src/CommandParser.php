<?php

namespace Brickhouse\Console;

use Brickhouse\Console\Attributes\Argument;
use Brickhouse\Console\Attributes\Option;
use Brickhouse\Console\Exceptions\OptionDoesntAcceptValuesException;
use Brickhouse\Console\Exceptions\RequiredArgumentException;
use Brickhouse\Console\Exceptions\RequiredOptionException;
use Brickhouse\Reflection\ReflectedProperty;
use Brickhouse\Reflection\ReflectedType;
use Toolkit\PFlag\Flags;
use Toolkit\PFlag\FlagType;

class CommandParser
{
    public function __construct(
        public readonly Console $console
    ) {}

    /**
     * Creates a new instance of a command class.
     *
     * @param class-string<Command>     $commandClass
     * @param array<array-key,mixed>    $argv
     *
     * @return Command
     */
    public function createCommand(string $commandClass, array $argv): Command
    {
        $command = new $commandClass($this->console);

        $this->parseCommandFlags($command, $argv);

        return $command;
    }

    /**
     * Parses the given flags and sets them on the given command instance.
     *
     * @param Command       $command
     * @param list<string>  $argv
     *
     * @return void
     */
    protected function parseCommandFlags(Command &$command, array $argv): void
    {
        $commandFlags = $this->getCommandFlags($command);

        $flags = Flags::new();
        $flags->setStopOnFistArg(false);
        $flags->setHelpRenderer(fn() => $this->printCommandHelp($command, $flags));

        /** @var array<string,array{0:ReflectedProperty,1:Option}> $options */
        $options = array_filter($commandFlags, fn(array $flag) => $flag[1] instanceof Option);

        /** @var array<string,array{0:ReflectedProperty,1:Argument}> $arguments */
        $arguments = array_filter($commandFlags, fn(array $flag) => $flag[1] instanceof Argument);

        uasort($arguments, function (array $a, array $b) {
            $a = $a[1]->order ?? 0;
            $b = $b[1]->order ?? 0;

            return $a <=> $b;
        });

        foreach ($arguments as [$property, $argument]) {
            $this->parseCommandArgument($flags, $property, $argument);
        }

        foreach ($options as [$property, $option]) {
            $this->parseCommandOption($flags, $property, $option);
        }

        if (!$flags->parse($argv)) {
            // Prints the help page.
            exit(0);
        }

        try {
            foreach ($commandFlags as [$property, $flag]) {
                $this->setCommandFlag($flags, $command, $property, $flag);
            }
        } catch (OptionDoesntAcceptValuesException | RequiredOptionException | RequiredArgumentException $e) {
            $flags->displayHelp();

            $command->newline();
            $command->error($e->getMessage());
            $command->newline();

            exit(1);
        }
    }

    /**
     * Parses the given command option and adds it the the `$flags` instance.
     *
     * @param Flags                 $flags
     * @param ReflectedProperty     $property
     * @param Option                $option
     *
     * @return void
     */
    protected function parseCommandOption(Flags $flags, ReflectedProperty $property, Option $option): void
    {
        $propertyType = $this->getCommandFlagType($property);
        $flagType = $this->determineFlagType($propertyType);

        $flags->addOpt(
            $option->name,
            $option->shortName ?? '',
            $option->description ?? '',
            $flagType,
            moreInfo: [
                'aliases' => $option->input === InputOption::NEGATABLE
                    ? ['no-' . $option->name]
                    : [],
                'helpType' => $flagType,
            ]
        );
    }

    /**
     * Parses the given command argument and adds it the the `$flags` instance.
     *
     * @param Flags                 $flags
     * @param ReflectedProperty     $property
     * @param Argument              $argument
     *
     * @return void
     */
    protected function parseCommandArgument(Flags $flags, ReflectedProperty $property, Argument $argument): void
    {
        $propertyType = $this->getCommandFlagType($property);
        $flagType = $this->determineFlagType($propertyType);

        $flags->addArg(
            $argument->name,
            $argument->description ?? '',
            $flagType,
        );
    }

    /**
     * Determines which flag type to use for the given property type.
     *
     * @param null|\ReflectionNamedType $type
     *
     * @return string
     */
    protected function determineFlagType(null|\ReflectionNamedType $type): string
    {
        if (!$type) {
            return FlagType::MIXED;
        }

        return match ($type->getName()) {
            'array' => FlagType::ARRAY,
            'bool' => FlagType::BOOL,
            'callable' => FlagType::CALLABLE,
            'float' => FlagType::FLOAT,
            'int' => FlagType::INT,
            'object' => FlagType::OBJECT,
            'string' => FlagType::STRING,
            default => FlagType::MIXED,
        };
    }

    /**
     * Get all the command flags for the given command.
     *
     * @param Command       $command
     *
     * @return array<string,array{0:ReflectedProperty,1:Option|Argument}>
     */
    protected function getCommandFlags(Command &$command): array
    {
        $reflector = new ReflectedType($command::class);

        /** @var array<string,list{ReflectedProperty,Option|Argument}> $flags */
        $flags = [];

        foreach ($reflector->getProperties() as $property) {
            if (!$property->public()) {
                continue;
            }

            $attributes = $property->attributes([Option::class, Argument::class], inherit: true);
            if (empty($attributes)) {
                continue;
            }

            foreach ($attributes as $attribute) {
                /** @var Option|Argument $flag */
                $flag = $attribute->create();

                $flags[$property->name] = [$property, $flag];
            }
        }

        return $flags;
    }

    /**
     * Get the type of the given command flag.
     *
     * @param ReflectedProperty     $property
     *
     * @return null|\ReflectionNamedType
     */
    protected function getCommandFlagType(ReflectedProperty $property): null|\ReflectionNamedType
    {
        $type = $property->type();
        if (!$type) {
            return null;
        }

        if (!$type instanceof \ReflectionNamedType) {
            return null;
        }

        return $type;
    }

    /**
     * Creates a new instance of a command class.
     *
     * @param Command           $command
     * @param ReflectedProperty $property
     * @param Option|Argument   $flag
     *
     * @return void
     */
    protected function setCommandFlag(Flags $flags, Command &$command, ReflectedProperty $property, Option|Argument $flag): void
    {
        if ($flag instanceof Option) {
            $value = $flags->getOption($flag->name)?->getValue();

            if ($flag->input === InputOption::NEGATABLE && in_array('--no-' . $flag->name, $flags->getFlags())) {
                $value = false;
            }
        } else {
            $value = $flags->getArgument($flag->name)?->getValue();
        }

        if ($flag->input === InputOption::REQUIRED && $value === null) {
            if ($flag instanceof Argument) {
                throw new RequiredArgumentException($flag->name);
            }

            if (isset($flags->getFlags()['--' . $flag->name])) {
                throw new RequiredOptionException($flag->name);
            }
        }

        if ($flag->input === InputOption::NONE && $value !== null) {
            throw new OptionDoesntAcceptValuesException($flag->name);
        }

        $value ??= $property->default;

        $property->setValue($command, $value);
    }

    /**
     * Prints a help page about the current command.
     *
     * @param Command   $command
     * @param Flags     $flags
     *
     * @return void
     */
    protected function printCommandHelp(Command $command, Flags $flags): void
    {
        \Termwind\render(<<<HTML
            <p class="mb-0">
                <span class="text-orange-300 mr-1 font-bold">
                    DESCRIPTION:
                </span>
                {$command->description}
            </p>
        HTML);

        \Termwind\render(<<<HTML
            <p class="mb-0">
                <span class="text-orange-300 mr-1 font-bold">
                    USAGE:
                </span>
                php brickhouse {$command->name}
                <span class="text-gray">[options] [arguments]</span>
            </p>
        HTML);

        if (!empty($flags->getOptions())) {
            \Termwind\render('<p class="text-orange-300 mr-1 mb-0 font-bold">Options:</p>');

            foreach ($flags->getOptions() as $option) {
                $name = $option->getHelpName();
                $type = $option->getHelpType(useTypeOnEmpty: true);
                $description = $option->getDesc(true);

                \Termwind\render(<<<HTML
                    <div class="flex space-x-1 max-w-full mr-2 ml-4 mb-0 mt-0">
                        <span>
                            {$name} <span class='text-gray-500 ml-1'>{$type}</span>
                        </span>
                        <span class="flex-1 text-gray-400 text-right">
                            {$description}
                        </span>
                    </div>
                HTML);
            }
        }

        if (!empty($flags->getArguments())) {
            \Termwind\render('<p class="text-orange-300 mr-1 mb-0 font-bold">Arguments:</p>');

            /** @var \Toolkit\PFlag\Flag\Argument $argument */
            foreach ($flags->getArguments() as $argument) {
                $name = strtoupper($argument->getHelpName());
                $type = $argument->getHelpType(useTypeOnEmpty: true);
                $description = $argument->getDesc(true);

                \Termwind\render(<<<HTML
                    <div class="flex space-x-1 max-w-full mr-2 ml-4 mb-0 mt-0">
                        <span>
                            {$name} <span class='text-gray-500 ml-1'>{$type}</span>
                        </span>
                        <span class="flex-1 text-gray-400 text-right">
                            {$description}
                        </span>
                    </div>
                HTML);
            }
        }
    }
}
