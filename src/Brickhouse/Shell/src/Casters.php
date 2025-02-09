<?php

namespace Brickhouse\Shell;

use Brickhouse\Core\Application;
use Brickhouse\Database\Transposer\Model;
use Brickhouse\Support\Arrayable;
use Brickhouse\Support\Collection;

class Casters
{
    /**
     * Cast the collection to an array.
     *
     * @template TKey of array-key
     * @template TValue
     *
     * @param Collection<TKey,TValue>   $collection
     *
     * @return array<TKey,TValue>
     */
    public static function castApplication(Application $application): array
    {
        $values = [];
        $properties = [
            'basePath',
            'vendorPath',
            'appPath',
            'configPath',
            'storagePath',
            'resourcePath',
            'publicPath'
        ];

        foreach ($properties as $property) {
            if (!isset($application->$property)) {
                continue;
            }

            $values[$property] = $application->$property;
        }

        $values['extensions'] = $application->extensions()->keys();
        $values['commands'] = $application->commands();

        return $values;
    }

    /**
     * Cast the collection to an array.
     *
     * @template TKey of array-key
     * @template TValue
     *
     * @param Collection<TKey,TValue>   $collection
     *
     * @return array<TKey,TValue>
     */
    public static function castCollection(Collection $collection): array
    {
        return $collection->toArray();
    }

    /**
     * Cast the arrayable to an array.
     *
     * @template TKey of array-key
     * @template TValue
     *
     * @param Arrayable<TKey,TValue>    $arrayable
     *
     * @return array<TKey,TValue>
     */
    public static function castArrayable(Arrayable $arrayable): array
    {
        return $arrayable->toArray();
    }

    /**
     * Cast a database model to an array.
     *
     * @param Model     $model
     *
     * @return array<string,mixed>
     */
    public static function castTransposerModel(Model $model): array
    {
        return $model->getProperties(include_relations: true);
    }
}
