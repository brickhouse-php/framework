<?php

namespace Brickhouse\Database\Transposer\Concerns;

use Brickhouse\Database\Transposer\Model;
use Brickhouse\Database\Transposer\Relations\Relation;
use Brickhouse\Reflection\ReflectedType;
use Brickhouse\Support\Collection;

trait HasRelations
{
    use HasAttributes,
        HasModelQuery;

    /**
     * Contains the currently loaded relations on the model.
     *
     * @var array<string,Model|Collection<int,Model>>
     */
    private array $loadedRelations = [];

    /**
     * Loads the relation with the given name into the model instance.
     *
     * @param string    $name           Name of the relation to load.
     *
     * @return static
     */
    public function load(string $name): static
    {
        $modelRelations = $this->getModelRelations();

        if (!isset($modelRelations[$name])) {
            throw new \RuntimeException("Invalid model relation '{$name}' on model " . self::class);
        }

        /** @var Relation<Model> $relation */
        $relation = $modelRelations[$name];

        $this->applyRelation($name, $relation->getAll($this));

        return $this;
    }

    /**
     * Loads the relation with the given name into the model instance, if it doesn't already exist on the model.
     *
     * @param string    $name   Name of the relation to load.
     *
     * @return void
     */
    public function loadIfMissing(string $name): void
    {
        if (!$this->loaded($name)) {
            $this->load($name);
        }
    }

    /**
     * Determines whether the relation with the given name is loaded.
     *
     * @param string    $name   Name of the relation.
     *
     * @return bool
     */
    public function loaded(string $name): bool
    {
        return isset($this->loadedRelations[$name]);
    }

    /**
     * Applies the given models to the relation on the current model.
     *
     * @param string                    $name   Name of the relation to apply.
     * @param Collection<int,Model>     $models Models to apply to the relation.
     *
     * @return void
     */
    private function applyRelation(string $name, Collection $models): void
    {
        $property = new ReflectedType($this::class)->getProperty($name);

        if (!($type = $property->type()) instanceof \ReflectionNamedType) {
            $this->$name = $models;
            return;
        }

        if (is_subclass_of($type->getName(), Model::class)) {
            $this->$name = $models->items()[0];
            return;
        }

        if ($type->getName() === Collection::class) {
            $this->$name = $models;
        } else if ($type->getName() === 'array') {
            $this->$name = $models->toArray();
        } else {
            throw new \RuntimeException(
                'Property does not accept related model: expected ' . $this::class . "::{$name} to be an array or " . Collection::class . ', found ' . $type->getName()
            );
        }
    }

    /**
     * Gets the names of all relations on the model, which have a value attached.
     *
     * @return array<int,string>
     */
    public function getDefinedRelations(): array
    {
        $relations = [];

        foreach (array_keys($this->getModelRelations()) as $name) {
            if (isset($this->$name)) {
                $relations[] = $name;
            }
        }

        return $relations;
    }
}
