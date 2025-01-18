<?php

namespace Brickhouse\Log;

use Brickhouse\Core\Application;
use Monolog\Handler\NullHandler;

/**
 * @phpstan-import-type Handlers from LogConfig
 */
class Extension extends \Brickhouse\Core\Extension
{
    /**
     * Gets the human-readable name of the extension.
     */
    public string $name = "brickhouse/log";

    public function __construct(private readonly Application $application) {}

    /**
     * Invoked before the application has started.
     */
    public function register(): void
    {
        $this->application->singletonIf(LogConfig::class, function () {
            return new LogConfig(
                default: "default",
                // @phpstan-ignore argument.type
                channels: [
                    "default" => new NullHandler(),
                ]
            );
        });

        $this->application->singleton(LoggerCache::class);

        $loggerCache = $this->application->resolve(LoggerCache::class);

        foreach (resolve(LogConfig::class)->channels as $name => $channel) {
            $logger = Logger::build($channel, $name);

            $loggerCache->add($name, $logger);
        }
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
