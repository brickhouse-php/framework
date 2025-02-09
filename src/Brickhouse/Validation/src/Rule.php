<?php

namespace Brickhouse\Validation;

/**
 * @phpstan-type RuleCondition      callable-string|\Closure(self):bool
 */
abstract class Rule
{
    /**
     * Defines the entire dataset being validated.
     *
     * @var mixed
     */
    protected mixed $data;

    /**
     * @param string                $message    Custom message to return if validation fails.
     * @param null|RuleCondition    $if         Predicate to satisfy before attempting validation.
     * @param null|RuleCondition    $unless     Predicate to not satisfy before attempting validation.
     */
    public function __construct(
        public readonly string $message,
        public readonly null|string|\Closure $if = null,
        public readonly null|string|\Closure $unless = null,
    ) {}

    /**
     * Validates the given input against the rule.
     *
     * @param string    $key
     * @param mixed     $value
     *
     * @return null|string
     */
    public abstract function validate(string $key, mixed $value): null|string;

    /**
     * Sets the dataset being validated in the rule.
     *
     * @param mixed     $data
     *
     * @return void
     */
    public function setData(mixed $data): void
    {
        $this->data = $data;
    }
}
