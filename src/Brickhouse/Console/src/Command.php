<?php

namespace Brickhouse\Console;

abstract class Command
{
    use \Brickhouse\Console\Concerns\WritesToConsole;

    /**
     * The name of the console command.
     *
     * @var string
     */
    public abstract string $name { get; }

    /**
     * The description of the console command.
     *
     * @var string
     */
    public string $description = '';

    /**
     * The help text of the console command.
     *
     * @var string
     */
    public string $help = '';

    /**
     * Creates a new instance of `Command`.
     */
    public final function __construct(
        public readonly Console $console,
    ) {}

    /**
     * Executes the console command.
     *
     * @return void|int
     */
    public abstract function handle();

    /**
     * Call a Brickhouse command with the given arguments.
     *
     * @param string                    $command        Name of the command to run.
     * @param array<array-key,mixed>    $args           Optional argumemts to pass to the command.
     *
     * @return integer
     */
    public final function call(string $command, array $args = []): int
    {
        return $this->console->execute($command, $args);
    }
}
