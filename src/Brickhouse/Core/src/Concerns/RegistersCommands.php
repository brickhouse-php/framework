<?php

namespace Brickhouse\Core\Concerns;

use Brickhouse\Console\Command;
use Brickhouse\Core\Application;

trait RegistersCommands
{
    /**
     * Adds the given command to the application.
     *
     * @param class-string<Command> $command
     *
     * @return void
     */
    public function addCommand(string $command): void
    {
        Application::current()->addCommand($command);
    }

    /**
     * Adds the given commands to the application.
     *
     * @param array<int,class-string<Command>>  $commands
     *
     * @return void
     */
    public function addCommands(array $commands): void
    {
        Application::current()->addCommands($commands);
    }
}
