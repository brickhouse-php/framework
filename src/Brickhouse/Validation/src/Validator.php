<?php

namespace Brickhouse\Validation;

use Brickhouse\Support\Arrayable;

/**
 * @phpstan-type    RuleList            array<int,Rule>
 * @phpstan-type    RuleSet             array<string,RuleList>
 */
class Validator
{
    /**
     * @param RuleSet   $rules
     */
    public function __construct(
        public readonly array $rules,
    ) {}

    /**
     * Validates the given object or array.
     *
     * @param object|array<string,mixed>    $data       Data to be validated.
     *
     * @return ValidationResult
     */
    public function validate(object|array $data): ValidationResult
    {
        $errors = $this->validateObject($data);

        return new ValidationResult($errors);
    }

    /**
     * Validates the given object within a nested object.
     *
     * @param object|array<string,mixed>    $data       Data to be validated.
     *
     * @return array<string,list<string>>
     */
    protected function validateObject(object|array $data): array
    {
        $dataAsArray = match (true) {
            $data instanceof Arrayable => $data->toArray(),
            is_array($data) => $data,
            default => get_object_vars($data),
        };

        $errors = [];

        foreach ($this->rules as $key => $rules) {
            $values = $this->getDottedValues($key, $dataAsArray);

            foreach ($rules as $rule) {
                $rule->setData($data);

                if (!$this->isRulePredicateSatisfied($rule, $data)) {
                    continue;
                }

                $errors[$key] = $this->validateValues($key, $values, $rule);
            }
        }

        return $errors;
    }

    /**
     * Retrieves all the values from `$value` which matches the given dotted key.
     *
     * @param string            $key        Dotted key to retrieve.
     * @param mixed             $value      Value to retrieve the key from.
     *
     * @return array<string,mixed>
     */
    protected function getDottedValues(string $key, mixed $value): array
    {
        $keys = explode('.', $key, limit: 2);

        if (count($keys) === 1) {
            $key = $keys[0];
            $value = $this->getValue($key, $value);

            return [$key => $value];
        }

        $key = array_shift($keys);
        $value = $this->getValue($key, $value);

        return $this->getDottedValues(implode('.', $keys), $value);
    }

    /**
     * Gets the given key from the value.
     *
     * @param string        $key        Key to retrieve from `$value`.
     * @param mixed         $value      Value to retrieve `$key` from.
     *
     * @return mixed
     */
    protected function getValue(string $key, mixed $value): mixed
    {
        return match (true) {
            $key === '*' && is_array($value) => $value,
            $key === '*' && is_object($value) => get_object_vars($value),
            is_array($value) => $value[$key] ?? null,
            is_object($value) => $value->$key ?? null,
            default => $value
        };
    }

    /**
     * Validates the given value within a nested object.
     *
     * @param string                $key        Key of the value being validated.
     * @param array<string,mixed>   $values     Values being validated.
     * @param Rule                  $rule       Rules to validate against.
     *
     * @return list<string>
     */
    protected function validateValues(string $key, array $values, Rule $rule): array
    {
        $errors = [];

        foreach ($values as $value) {
            $result = $rule->validate($key, $value);

            if ($result !== null) {
                $errors[] = $result;
            }
        }

        return $errors;
    }

    /**
     * Determines whether the given rule should be validated.
     *
     * @param Rule      $rule       Rule to check predicates of.
     * @param mixed     $value      Root value to pass to callable predicates.
     *
     * @return bool
     */
    protected function isRulePredicateSatisfied(Rule $rule, mixed $value): bool
    {
        if (isset($rule->if) && !($rule->if)($value)) {
            return false;
        }

        if (isset($rule->unless) && ($rule->unless)($value)) {
            return false;
        }

        return true;
    }

    /**
     * Asserts that validation of the given object or array is successful.
     *
     * @param object|array<string,mixed>    $data       Data to be validated.
     *
     * @return void
     *
     * @throws ValidationFailedException    Thrown if validation fails.
     */
    public function assert(object|array $data): void
    {
        if (($result = $this->validate($data))->invalid) {
            throw new ValidationFailedException($result);
        }
    }
}
