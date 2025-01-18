<?php

namespace Brickhouse\Console\Exceptions;

class RequiredOptionException extends \Exception
{
    public function __construct(
        public readonly string $option
    ) {
        parent::__construct("Required option was not supplied a value: {$option}");
    }
}
