<?php

namespace Brickhouse\Database\Transposer;

use Brickhouse\Database\Builder\QueryBuilder;
use Brickhouse\Database\Transposer\Concerns;
use Brickhouse\Support\Arrayable;
use Brickhouse\Support\Collection;

abstract class Model implements \JsonSerializable
{
    use Concerns\HasAttributes,
        Concerns\HasModelQuery,
        Concerns\HasNamingStrategy,
        Concerns\HasRelations;

    /**
     * Gets the ID of the model.
     *
     * @var null|int|string
     */
    public $id = null;

    /**
     * Gets whether the model exists in the database.
     *
     * @var bool
     */
    public bool $exists {
        get => $this->id !== null;
    }

    public final function __construct() {}

    /**
     * Creates a new instance of the model without saving it to the database.
     * Optionally, fills the given properties into the model.
     *
     * @param array<string,mixed>   $properties     Optional properties to fill into the model on creation.
     *
     * @return static
     */
    public static function new(array $properties = []): static
    {
        /** @var self $model */
        $model = resolve(ModelBuilder::class)
            ->create(static::class, $properties);

        return $model;
    }

    /**
     * Creates a new instance of the model and saves it to the database.
     * Optionally, fills the given properties into the model.
     *
     * @param array<string,mixed>   $properties     Optional properties to fill into the model on creation.
     *
     * @return static
     */
    public static function create(array $properties = []): static
    {
        return static::new($properties)->save();
    }

    /**
     * Prints a string-representation of the model to STDOUT.
     *
     * @return void
     */
    public function inspect(): void
    {
        echo json_encode($this->jsonSerialize(), JSON_PRETTY_PRINT) . PHP_EOL;
    }

    /**
     * Saves the model to the database and returns the model with it's updated database values.
     *
     * @return static
     */
    public function save(): static
    {
        $query = $this->query();

        if ($this->exists) {
            $model = $query->update($this);
        } else {
            $model = $query->insert($this);
        }

        $model->setOriginalAttributes(
            $model->getProperties(include_relations: true)
        );

        /** @var self $model */
        return $model;
    }

    /**
     * Refreshes the model with updated values from the database and returns it.
     *
     * @return static
     */
    public function refresh(): static
    {
        if (!$this->exists) {
            throw new \RuntimeException("Attempted to update model which doesn't exist (" . static::class . ")");
        }

        $updated = $this->query()->find($this->id);
        if (!$updated) {
            throw new \RuntimeException("Could not update model: database entry does not exist (" . static::class . ")");
        }

        $this->setOriginalAttributes(
            $updated->getProperties(include_relations: true)
        );

        $this->fill($updated->getProperties());

        return $this;
    }

    /**
     * Saves all the relational attributes on the model.
     *
     * @return void
     */
    public function persistRelationalAttributes(): void
    {
        foreach ($this->getDirtyAttributes() as $attribute => $value) {
            if (!($relation = $this->getModelRelation($attribute))) {
                continue;
            }

            /** @var list<Model> $relatedModels */
            $relatedModels = match (true) {
                $value instanceof Collection => $value->toArray(),
                $value instanceof Model => [$value],
                $value instanceof Arrayable => $value->toArray(),
                default => (array) $value,
            };

            $foreignColumnName = $this::naming()->foreignKey();
            $queryBuilder = $this::query()->builder->from($relation->model::table());

            foreach ($relatedModels as $relatedModel) {
                $relationModelAttributes = $relatedModel->getInsertableAttributes();
                $relationModelAttributes[$foreignColumnName] = $this->id;

                if ($relatedModel->exists) {
                    $queryBuilder->update($relationModelAttributes);
                } else {
                    $queryBuilder->insert($relationModelAttributes);
                }
            }
        }
    }

    public function jsonSerialize(): mixed
    {
        $properties = $this->getProperties(include_relations: true);

        foreach ($properties as $idx => $property) {
            // Serialize nested models as well
            if ($property instanceof Model) {
                $properties[$idx] = $property->jsonSerialize();
            }
        }

        return $properties;
    }
}
