<?php

namespace Brickhouse\Config;

final class ConfigCache
{
    /**
     * Gets all the configuration files in the application.
     *
     * @var array<string,Config>
     */
    private array $configs = [];

    /**
     * Gets all the configuration objects in the cache.
     *
     * @return array<string,Config>
     */
    public function configs(): array
    {
        return $this->configs;
    }

    public function addConfig(string $path): void
    {
        $filename = basename($path);

        $config = require_once $path;
        if (!$config || !$config instanceof Config) {
            throw new \Exception(
                "Configuration file returned invalid config: " .
                    ($config ? $config::class : "null")
            );
        }

        $this->configs[$filename] = $config;
    }
}
