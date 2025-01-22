<?php

namespace Brickhouse\Cache;

use Brickhouse\Config\Config;

final class CacheConfig extends Config
{
    /**
     * Creates a new instance of `CacheConfig`.
     *
     * @param string                        $default    Defines the default cache if none is provided.
     * @param array<string,CacheProvider>   $providers  Defines the available cache providers.
     */
    public function __construct(
        public readonly string $default = "",
        public readonly array $providers = [],
    ) {}
}
