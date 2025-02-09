<?php

namespace Brickhouse\Validation\Rules;

use Brickhouse\Validation\Rule;

/**
 * Validates that the input is a value other than the given value.
 */
class OtherThan extends Rule
{
    public function __construct(
        public readonly mixed $value,
        public readonly bool $strict = false,
        string $message = 'Field {attribute} must be other than {value}.',
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
        if ($this->strict && $value === $this->value) {
            return $this->message;
        } else if (!$this->strict && $value == $this->value) {
            return $this->message;
        }

        return null;
    }
}
