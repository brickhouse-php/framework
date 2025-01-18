<?php

namespace Brickhouse\Log;

use Brickhouse\Config\Config;

/**
 * @phpstan-type HandlerType    \Monolog\Handler\StreamHandler
 * @phpstan-type Handlers       (HandlerType)|(HandlerType[])
 */
final class LogConfig extends Config
{
    /**
     * Creates a new instance of `LogConfig`.
     *
     * @param string                        $default    Defines the default channel if none is provided.
     * @param array<string,Handlers>        $channels   Defines the channels available to log to.
     */
    public function __construct(
        public readonly string $default = "",
        public readonly array $channels = [],
    ) {}
}
