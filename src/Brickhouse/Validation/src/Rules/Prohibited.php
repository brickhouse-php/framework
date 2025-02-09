<?php

namespace Brickhouse\Validation\Rules;

use Brickhouse\Validation\Rule;

/**
 * Validates that the input is not set (`null` or empty).
 */
class Prohibited extends Rule
{
    public function __construct(
        string $message = 'Field {attribute} is prohibited.',
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
        if ($value !== null && !empty($value)) {
            return $this->message;
        }

        return null;
    }
}
