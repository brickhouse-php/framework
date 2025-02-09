<?php

namespace Brickhouse\Validation;

class ValidationResult
{
    /**
     * Determines whether the validation was successful.
     *
     * @var bool
     */
    public bool $valid {
        get => array_all($this->errors, fn(array $errors) => empty($errors));
    }

    /**
     * Determines whether the validation failed.
     *
     * @var bool
     */
    public bool $invalid {
        get => !$this->valid;
    }

    /**
     * Defines all the errors in the individual keys.
     *
     * @var array<string,list<string>>
     */
    public readonly array $errors;

    /**
     * @param array<string,list<string>>    $errors     Defines all the errors in the individual keys.
     */
    public function __construct(array $errors = [])
    {
        $this->errors = array_filter($errors, fn(array $errors) => !empty($errors));
    }
}
