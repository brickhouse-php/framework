<?php

namespace Brickhouse\Shell;

use Brickhouse\Console\Attributes\Argument;
use Brickhouse\Console\Attributes\Option;
use Brickhouse\Console\Command;
use Brickhouse\Console\InputOption;
use Brickhouse\Reflection\ReflectedType;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption as SymfonyInputOption;
use Symfony\Component\Console\Input\InputArgument as SymfonyInputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class CommandWrapper extends \Psy\Command\Command
{
    public function __construct(
        public readonly Command $wrappedCommand,
    ) {
        parent::__construct(null);
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName($this->wrappedCommand->name)
            ->setDefinition([])
            ->setDescription($this->wrappedCommand->description)
            ->setHelp($this->wrappedCommand->help);

        foreach ($this->getUnwrappedArguments() as $property => $attribute) {
            $argumentType = match ($attribute->input) {
                InputOption::REQUIRED => SymfonyInputArgument::REQUIRED,
                InputOption::OPTIONAL => SymfonyInputArgument::OPTIONAL,
            };

            $this->addArgument(
                $attribute->name,
                $argumentType,
                $attribute->description ?? ''
            );
        }

        foreach ($this->getUnwrappedOptions() as $attribute) {
            $optionType = match ($attribute->input) {
                InputOption::NONE => SymfonyInputOption::VALUE_NONE,
                InputOption::REQUIRED => SymfonyInputOption::VALUE_REQUIRED,
                InputOption::OPTIONAL => SymfonyInputOption::VALUE_OPTIONAL,
                InputOption::NEGATABLE => SymfonyInputOption::VALUE_NEGATABLE,
            };

            $this->addOption(
                $attribute->name,
                $attribute->shortName,
                $optionType,
                $attribute->description ?? ''
            );
        }
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$input instanceof ArgvInput) {
            throw new \RuntimeException("Invalid input interface. Expected ArgvInput, found " . $input::class);
        }

        return $this->wrappedCommand->console->execute(
            $this->wrappedCommand->name,
            $input->getRawTokens()
        );
    }

    /**
     * Gets all the options for the unwrapped command.
     *
     * @return array<string,Option>
     */
    protected function getUnwrappedOptions(): array
    {
        $options = [];

        foreach (new ReflectedType($this->wrappedCommand::class)->getProperties() as $property) {
            $attribute = $property->attribute(Option::class, inherit: true);
            if ($attribute === null) {
                continue;
            }

            $options[$property->name] = $attribute->create();
        }

        return $options;
    }

    /**
     * Gets all the arguments for the unwrapped command.
     *
     * @return array<string,Argument>
     */
    protected function getUnwrappedArguments(): array
    {
        $arguments = [];

        foreach (new ReflectedType($this->wrappedCommand::class)->getProperties() as $property) {
            $attribute = $property->attribute(Argument::class, inherit: true);
            if ($attribute === null) {
                continue;
            }

            $arguments[$property->name] = $attribute->create();
        }

        return $arguments;
    }
}
