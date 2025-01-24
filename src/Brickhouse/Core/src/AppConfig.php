<?php

namespace Brickhouse\Core;

use Brickhouse\Config\Config;

final class AppConfig extends Config
{
    /**
     * Creates a new instance of `AppConfig`.
     *
     * @param bool      $debug      Defines whether the application is in debug-mode.
     * @param bool      $api_only   Defines whether the application only supports API controllers.
     */
    public function __construct(
        public readonly bool $debug = false,
        public readonly bool $api_only = false,
    ) {}
}
