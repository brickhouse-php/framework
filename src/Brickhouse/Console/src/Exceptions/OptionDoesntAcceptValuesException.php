<?php

namespace Brickhouse\Console\Exceptions;

class OptionDoesntAcceptValuesException extends \Exception
{
    public function __construct(
        public readonly string $option
    ) {
        parent::__construct("Option {$option} does not accept values.");
    }
}
