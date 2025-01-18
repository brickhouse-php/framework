<?php

namespace Brickhouse\Core\Console\Concerns;

use Brickhouse\Console\Command;
use Brickhouse\Console\Console;

trait HandlesCommands
{
    /**
     * Gets all the available commands in the application.
     *
     * @var array<int,class-string<Command>>
     */
    protected array $commands = [];

    /**
     * Gets all the commands of the application.
     *
     * @return array<int,class-string<Command>>
     */
    public function commands(): array
    {
        return $this->commands;
    }

    /**
     * Adds the given command to the application.
     *
     * @param class-string<Command>     $command
     */
    public function addCommand(string $command): void
    {
        $this->commands[] = $command;
    }

    /**
     * Adds the given commands to the application.
     *
     * @param array<int,class-string<Command>>  $commands
     */
    public function addCommands(array $commands): void
    {
        foreach ($commands as $command) {
            $this->addCommand($command);
        }
    }

    /**
     * Starts the application and run the command matching the input.
     *
     * @return int
     */
    public function handleCommand(): int
    {
        $application = new Console('Blowdart', '1.0.0');
        $application->addCommands($this->commands);

        return $application->run();
    }
}
