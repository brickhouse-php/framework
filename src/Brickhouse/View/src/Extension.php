<?php

namespace Brickhouse\View;

use Brickhouse\Core\Application;
use Brickhouse\View\Commands;

class Extension extends \Brickhouse\Core\Extension
{
    /**
     * Gets the human-readable name of the extension.
     */
    public string $name = 'brickhouse/view';

    public function __construct(
        private readonly Application $application
    ) {}

    /**
     * Invoked before the application has started.
     */
    public function register(): void
    {
        $this->application->singleton(Renderer::class);
        $this->application->singleton(ViewFactory::class);

        $this->application->singleton(\Brickhouse\View\Contracts\Engine::class, Engine::class);
    }

    /**
     * Invoked after the application has started.
     */
    public function boot(): void
    {
        $this->addCommands([
            Commands\ComponentGenerator::class,
            Commands\LayoutGenerator::class,
            Commands\ViewGenerator::class,
            Commands\ViewsClear::class,
            Commands\ViewsCompile::class,
        ]);
    }
}
