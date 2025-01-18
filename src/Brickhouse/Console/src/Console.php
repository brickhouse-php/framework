<?php

namespace Brickhouse\Console;

use Brickhouse\Console\Commands\Help;

final class Console
{
    /**
     * List of all the commands in the console application.
     *
     * @var array<string,class-string<Command>>
     */
    public private(set) array $commands = [];

    /**
     * Gets the command parser for the console.
     *
     * @var CommandParser
     */
    private readonly CommandParser $parser;

    public function __construct(
        public readonly string $applicationName,
        public readonly null|string $applicationVersion = null,
    ) {
        $this->addCommand(Help::class);

        $this->parser = new CommandParser($this);
    }

    /**
     * Adds the given command to the console application.
     *
     * @param class-string<Command> $command    Command to add to the console application.
     *
     * @return void
     */
    public function addCommand(string $command): void
    {
        $reflector = new \ReflectionClass($command);
        $commandName = $reflector->getDefaultProperties()['name'];

        $this->commands[$commandName] = $command;
    }

    /**
     * Adds the given list of commands to the console application.
     *
     * @param list<class-string<Command>>   $commands   Commands to add to the console application.
     *
     * @return void
     */
    public function addCommands(array $commands): void
    {
        foreach ($commands as $command) {
            $this->addCommand($command);
        }
    }

    /**
     * Runs the console application and returns an integer-based exit code.
     *
     * @return int
     */
    public function run(): int
    {
        $argv = $_SERVER['argv'];

        // Shift the script name out of the arguments.
        array_shift($argv);

        // Get the command which was invoked.
        $commandName = array_shift($argv) ?? 'help';

        return $this->execute($commandName, $argv);
    }

    /**
     * Runs the given command and returns it's integer-based exit code.
     *
     * @param string                    $name   Name of the command to run.
     * @param array<array-key,mixed>    $args   Optional argumemts to pass to the command.
     *
     * @return int
     */
    public function execute(string $name, array $args = []): int
    {
        $commandClass = $this->commands[$name] ?? Help::class;
        $command = $this->parser->createCommand($commandClass, $args);

        $result = $command->handle();
        if (is_int($result)) {
            return $result;
        }

        return 0;
    }
}
