<?php

namespace Brickhouse\Validation\Rules;

use Brickhouse\Database\Transposer\Model;
use Brickhouse\Validation\Rule;

/**
 * Validates that the input model is unique within the database.
 */
class Unique extends Rule
{
    public function __construct(
        string $message = 'Field {attribute} is already taken.',
        public readonly null|array $scope = null,
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
        if ($value === null || !$this->data instanceof Model) {
            return $this->message;
        }

        $query = $this->data::query()
            ->where($key, '=', $this->data->$key)
            ->take(1);

        // Apply the scopes to the query builder, if the are defined.
        if ($this->scope !== null) {
            foreach ($this->scope as $scope) {
                $query = $query->where($scope, '=', $this->data->$scope);
            }
        }

        if ($query->first() !== null) {
            return $this->message;
        }

        return null;
    }
}
