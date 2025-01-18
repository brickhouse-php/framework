<?php

namespace Brickhouse\Core\Events;

use Brickhouse\Core\Application;

final readonly class ApplicationBooted
{
    public function __construct(
        public readonly Application $application
    ) {}
}
