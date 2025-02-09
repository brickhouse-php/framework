<?php

namespace Brickhouse\Validation\Rules;

use Brickhouse\Validation\Rule;

/**
 * Validates that the input is set to a truthy value.
 */
class Acceptance extends Rule
{
    public function __construct(
        string $message = 'Field {attribute} must be set.',
        public readonly array $matches = [],
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
        if ($value === null) {
            return $this->message;
        }

        if (in_array($value, $this->matches)) {
            return null;
        }

        return match (true) {
            is_string($value) && in_array(strtolower($value), ['yes', 'on', 'true', '1']) => null,
            is_int($value) && $value === 1 => null,
            is_bool($value) && $value === true => null,
            default => $this->message,
        };
    }
}
