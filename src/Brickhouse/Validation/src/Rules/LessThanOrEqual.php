<?php

namespace Brickhouse\Validation\Rules;

use Brickhouse\Validation\Rule;

/**
 * Validates that the input is less than or equal to the given value.
 */
class LessThanOrEqual extends Rule
{
    public function __construct(
        public readonly int $maximum,
        string $message = 'Field {attribute} must be less than or equal to {maximum}.',
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
        if ($value === null || !is_numeric($value) || $value > $this->maximum) {
            return $this->message;
        }

        return null;
    }
}
