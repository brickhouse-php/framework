<?php

namespace Brickhouse\Validation\Rules;

use Brickhouse\Validation\Rule;

/**
 * Validates that the input is between the two given values (inclusive).
 */
class Between extends Rule
{
    public function __construct(
        public readonly int $min,
        public readonly int $max,
        string $message = 'Field {attribute} must be between :min and :max in length.',
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
        if ($value === null || (!is_numeric($value) && !is_array($value))) {
            return $this->message;
        }

        $length = match (true) {
            is_array($value) => count($value),
            default => $value,
        };

        if ($length > $this->max || $length < $this->min) {
            return $this->message;
        }

        return null;
    }
}
