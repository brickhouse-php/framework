<?php

namespace Brickhouse\Support;

/**
 * @template TKey of array-key
 * @template TValue
 *
 * @extends \IteratorAggregate<TKey,TValue>
 * @extends \ArrayAccess<TKey,TValue>
 */
interface Enumerable extends \IteratorAggregate, \Countable, \ArrayAccess
{
    /**
     * @param array<TKey,TValue>    $items
     */
    public function __construct(array $items = []);

    /**
     * Gets all the keys of the items in the collection as an array.
     *
     * @return array<int,TKey>
     */
    public function keys(): array;

    /**
     * Gets all the items in the collection as an array.
     *
     * @return array<TKey,TValue>
     */
    public function items(): array;

    /**
     * Checks if at least one element in the collection satisfies the given callback function.
     *
     * If this callback returns `true`, the method returns `true` and the callback will not be called for further elements.
     *
     * @param \Closure(TValue $value, TKey $key): bool  $callback
     *
     * @return bool
     */
    public function any(\Closure $callback): bool;

    /**
     * Checks if all elements in the collection satisfies the given callback function.
     *
     * @param \Closure(TValue $value, TKey $key): bool  $callback
     *
     * @return bool
     */
    public function all(\Closure $callback): bool;

    /**
     * Splits the collection into chunks of, at least, `$length`-sized chunks.
     * If the length of the collection is not divisible by `$length`, the last chunk will be truncated accordingly.
     *
     * @param int   $length             Defines the maximum length for all the chunks.
     * @param bool  $preserve_keys      Whether to preserve the keys in the collection. Defaults to `true`.
     *
     * @return static<int,static>
     */
    public function chunk(int $length, bool $preserve_keys = true);

    /**
     * Merges the given array or collection into the original collection.
     * If a string key in the given already exists in the collection, the original value is overwritten.
     *
     * @param mixed     $values
     *
     * @return static<TKey,TValue>
     */
    public function merge(mixed $values): self;

    /**
     * Recursively merges the given array or collection into the original collection.
     * If a string key in the given already exists in the collection, the original value is overwritten.
     *
     * @param mixed     $values
     *
     * @return static<TKey,TValue>
     */
    public function mergeRecursive(mixed $values): self;

    /**
     * Push the given value onto the end of the collection.
     *
     * @param ($value is null ? TValue : TKey) $key
     * @param null|TValue $value
     *
     * @return static<TKey,TValue>
     */
    public function push($key, $value = null): self;

    /**
     * Map all values in the collection into a new collection.
     *
     * @template TNewValue
     *
     * @param callable(TValue $value,TKey $key):TNewValue $callback
     *
     * @return static<TKey,TNewValue>
     */
    public function map(callable $callback): self;

    /**
     * Invoke the given callback on all the items in the collection.
     *
     * @param callable(TValue $value,TKey $key):void $callback
     *
     * @return self<TKey,TValue>
     */
    public function each(callable $callback): self;

    /**
     * Flatten a multi-dimensional collection into a single level.
     *
     * @param int   $depth
     *
     * @return static<int,mixed>
     */
    public function flatten(int $depth = PHP_INT_MAX): self;

    /**
     * Map all values in the collection into a new collection.
     *
     * @param (callable(TValue $value, TKey $key): bool) $callback
     *
     * @return static<TKey,TValue>
     */
    public function filter(callable $callback): self;

    /**
     * Keys all the items in the array by a field in their values.
     *
     * @param string    $keyBy
     *
     * @return static<array-key,TValue>
     */
    public function keyBy(string $keyBy): self;

    /**
     * Sort the values using the given callback or field.
     *
     * @param (callable(TValue $value, TKey $key): int)|string $callback
     *
     * @return static<TKey,TValue>
     */
    public function sortBy(callable|string $callback, ?int $flags = null): self;

    /**
     * Retrives all the values for a given key. If `$value` is defined, uses the first argument as the keys.
     *
     * @param string        $keyOrValue
     * @param null|string   $value
     *
     * @return static<($value is null ? int : string),mixed>
     */
    public function pluck(string $keyOrValue, null|string $value = null): self;

    /**
     * Dump the values of the collection.
     *
     * @return void
     */
    public function dump(): void;
    /**
     * Joins all elements in the collection together.
     *
     * @param   string  $separator  The string to add between the elements.
     *
     * @return string
     */
    public function join(string $separator = ""): string;
}
