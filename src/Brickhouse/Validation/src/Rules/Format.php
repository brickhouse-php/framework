<?php

namespace Brickhouse\Validation\Rules;

use Brickhouse\Validation\Rule;

/**
 * Validates that the input matches the given Regex format.
 */
class Format extends Rule
{
    public function __construct(
        public readonly string $pattern,
        string $message,
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
        if ($value === null || !is_string($value) || !preg_match($this->pattern, $value)) {
            return $this->message;
        }

        return null;
    }
}
