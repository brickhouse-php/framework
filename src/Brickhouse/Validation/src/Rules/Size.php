<?php

namespace Brickhouse\Validation\Rules;

use Brickhouse\Validation\Rule;

/**
 * Validates that the input array is equal to the given size.
 */
class Size extends Rule
{
    public function __construct(
        public readonly int $count,
        string $message = 'Field {attribute} must contain {count} elements.',
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
        if ($value === null || !is_array($value)) {
            return $this->message;
        }

        if (count($value) !== $this->count) {
            return $this->message;
        }

        return null;
    }
}
