<?php

namespace Brickhouse\Cache;

use Brickhouse\Core\Application;

class Extension extends \Brickhouse\Core\Extension
{
    /**
     * Gets the human-readable name of the extension.
     */
    public string $name = "brickhouse/cache";

    public function __construct(private readonly Application $application) {}

    /**
     * Invoked before the application has started.
     */
    public function register(): void
    {
        $this->application->singletonIf(CacheConfig::class, function () {
            return new CacheConfig(
                default: "array",
                providers: [
                    "array" => new ArrayCache,
                ]
            );
        });
    }

    /**
     * Invoked after the application has started.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Defines all the extensions which need to be loaded first.
     */
    public function dependencies(): array
    {
        return [\Brickhouse\Config\Extension::class];
    }
}
