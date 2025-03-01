<?php

namespace Brickhouse\Database;

use Brickhouse\Core\Application;
use Brickhouse\Database\Commands;
use Brickhouse\Database\Transposer\ModelBuilder;

class Extension extends \Brickhouse\Core\Extension
{
    /**
     * Gets the human-readable name of the extension.
     */
    public string $name = "brickhouse/database";

    public function __construct(private readonly Application $application) {}

    /**
     * Invoked before the application has started.
     */
    public function register(): void
    {
        $this->application->singleton(ConnectionManager::class);
        $this->application->singleton(ModelBuilder::class);
    }

    /**
     * Invoked after the application has started.
     */
    public function boot(): void
    {
        $this->addCommands([
            Commands\Migrate::class,
            Commands\MigrationGenerator::class,
            Commands\Refresh::class,
            Commands\Reset::class,
            Commands\Rollback::class,
        ]);
    }
}
