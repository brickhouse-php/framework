<?php

namespace Brickhouse\Core\Events;

use Brickhouse\Core\Application;

final readonly class ApplicationBootstrapped
{
    public function __construct(
        public readonly Application $application
    ) {}
}
