<?php

namespace Brickhouse\Config;

use Brickhouse\Core\Application;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class Extension extends \Brickhouse\Core\Extension
{
    /**
     * Gets the human-readable name of the extension.
     */
    public string $name = "brickhouse/config";

    public function __construct(private readonly Application $application)
    {
    }

    /**
     * Invoked before the application has started.
     */
    public function register(): void
    {
        $this->loadEnvironment();
        $this->registerConfigurations();
    }

    /**
     * Invoked after the application has started.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Load the current environment into the application.
     *
     * @return void
     */
    protected function loadEnvironment(): void
    {
        $environmentFilePath = Environment::file();
        if ($environmentFilePath === null) {
            return;
        }

        $environmentVariables = parse_ini_file($environmentFilePath);
        if (!$environmentVariables) {
            return;
        }

        foreach ($environmentVariables as $key => $value) {
            putenv("{$key}={$value}");
        }
    }

    /**
     * Load configuration files into the application.
     *
     * @return void
     */
    protected function registerConfigurations(): void
    {
        $this->application->singleton(ConfigCache::class);

        foreach ($this->findConfigs() as $configPath) {
            $configInstance = require $configPath;

            $this->application->instance(
                $configInstance::class,
                $configInstance
            );
        }
    }

    /**
     * Find all configuration files in the application.
     *
     * @return array<int,string>
     */
    protected function findConfigs(): array
    {
        // If the configuration path doesn't exist, skip loading any.
        if (!is_dir($this->application->configPath)) {
            return [];
        }

        $finder = new Finder();
        $finder
            ->files()
            ->in($this->application->configPath)
            ->name("*.config.php");

        $files = iterator_to_array($finder->getIterator());

        return array_values(
            array_map(
                fn(SplFileInfo $file) => (string) $file->getRealPath(),
                $files
            )
        );
    }
}
