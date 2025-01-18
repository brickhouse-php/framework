<?php

namespace Brickhouse\Support;

/**
 * @template TKey
 * @template-covariant TValue
 */
interface Arrayable
{
    /**
     * Get the instance in the form of an array.
     *
     * @return array<TKey,TValue>
     */
    public function toArray(): array;
}
