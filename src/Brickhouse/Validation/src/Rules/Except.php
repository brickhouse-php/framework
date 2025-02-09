<?php

namespace Brickhouse\Validation\Rules;

use Brickhouse\Validation\Rule;

/**
 * Validates that the input is none of the given values.
 */
class Except extends Rule
{
    /**
     * @param class-string|list<mixed>      $value
     */
    public function __construct(
        public readonly string|array $value,
        string $message = 'Field {attribute} is a disallowed value.',
        public readonly bool $strict = false,
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
        if (is_array($this->value) && in_array($value, $this->value, $this->strict)) {
            return $this->message;
        }

        $options = match (true) {
            is_subclass_of($this->value, \UnitEnum::class) => $this->value::cases(),
            is_subclass_of($this->value, \BackedEnum::class) => $this->value::cases(),
            default => (array) $this->value
        };

        if (in_array($value, $options, $this->strict)) {
            return $this->message;
        }

        return null;
    }
}
