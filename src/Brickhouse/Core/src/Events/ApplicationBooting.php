<?php

namespace Brickhouse\Core\Events;

use Brickhouse\Core\Application;

final readonly class ApplicationBooting
{
    public function __construct(
        public readonly Application $application
    ) {}
}
