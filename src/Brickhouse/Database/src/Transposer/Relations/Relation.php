<?php

namespace Brickhouse\Database\Transposer\Relations;

use Brickhouse\Database\Transposer\Model;
use Brickhouse\Support\Collection;

/**
 * @template TModel of Model
 */
abstract class Relation
{
    /**
     * @param class-string<TModel>  $model
     */
    public function __construct(public readonly string $model) {}
}
