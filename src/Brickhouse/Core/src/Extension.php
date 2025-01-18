<?php

namespace Brickhouse\Core;

abstract class Extension
{
    use \Brickhouse\Core\Concerns\RegistersCommands;

    /**
     * Gets the human-readable name of the extension.
     */
    abstract public string $name { get; }

    /**
     * Gets the current version of the extension.
     */
    public string $version { get => ''; }

    /**
     * Invoked before the application has started.
     *
     * @return void
     */
    public abstract function register(): void;

    /**
     * Invoked after the application has started.
     *
     * @return void
     */
    public abstract function boot(): void;

    /**
     * Defines all the extensions which need to be loaded first.
     *
     * @return array<int,class-string<Extension>>
     */
    public function dependencies(): array
    {
        return [];
    }
}
