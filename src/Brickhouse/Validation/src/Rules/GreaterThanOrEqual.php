<?php

namespace Brickhouse\Validation\Rules;

use Brickhouse\Validation\Rule;

/**
 * Validates that the input is greater than or equal to the given value.
 */
class GreaterThanOrEqual extends Rule
{
    public function __construct(
        public readonly int $minimum,
        string $message = 'Field {attribute} must be greater than or equal to {value}.',
        null|string|\Closure $if = null,
        null|string|\Closure $unless = null,
    ) {
        parent::__construct($message, $if, $unless);
    }

    /**
     * @inheritdoc
     */
    public function validate(string $key, mixed $value): null|string
    {
        if ($value === null || !is_numeric($value) || $value < $this->minimum) {
            return $this->message;
        }

        return null;
    }
}
