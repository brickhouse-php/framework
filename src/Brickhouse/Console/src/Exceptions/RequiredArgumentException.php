<?php

namespace Brickhouse\Console\Exceptions;

class RequiredArgumentException extends \Exception
{
    public function __construct(
        public readonly string $argument
    ) {
        $argument = strtoupper($argument);

        parent::__construct("Required argument was not given: {$argument}");
    }
}
