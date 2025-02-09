<?php

namespace Brickhouse\Validation;

class ValidationFailedException extends \InvalidArgumentException
{
    public function __construct(
        public readonly ValidationResult $result,
    ) {}
}
