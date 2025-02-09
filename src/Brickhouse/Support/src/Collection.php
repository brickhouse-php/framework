<?php

namespace Brickhouse\Support;

/**
 * @template TKey of array-key
 * @template TValue
 *
 * @implements Enumerable<TKey, TValue>
 * @implements Arrayable<TKey, TValue>
 */
class Collection implements Enumerable, Renderable, Arrayable, \Stringable, \JsonSerializable
{
    /**
     * The items contained in the collection.
     *
     * @var array<TKey,TValue>
     */
    protected $items = [];

    /**
     * @param array<TKey,TValue>    $items
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * Undocumented function
     *
     * @template TMakeKey of array-key
     * @template TMakeValue
     *
     * @param iterable<TMakeKey,TMakeValue>|null $items
     *
     * @return static<TMakeKey,TMakeValue>
     */
    public static function make($items = []): Collection
    {
        return new Collection($items);
    }

    /**
     * Wrap the given value into a new collection.
     *
     * @param mixed     $value
     *
     * @return static<TKey,TValue>
     */
    public static function wrap(mixed $value): Collection
    {
        if (is_array($value) || is_iterable($value) || is_a($value, \Traversable::class)) {
            $value = iterator_to_array($value);
        }

        return match (true) {
            is_array($value) => new Collection($value),
            default => new Collection([$value]),
        };
    }

    /**
     * Create a new empty collection.
     *
     * @return static<TKey,TValue>
     */
    public static function empty(): Collection
    {
        return new Collection();
    }

    /**
     * @inheritDoc
     */
    public function keys(): array
    {
        return array_keys($this->items);
    }

    /**
     * @inheritDoc
     */
    public function items(): array
    {
        return $this->items;
    }

    /**
     * @inheritDoc
     */
    public function any(\Closure $callback): bool
    {
        return array_any($this->items, $callback);
    }

    /**
     * @inheritDoc
     */
    public function all(\Closure $callback): bool
    {
        return array_all($this->items, $callback);
    }

    /**
     * @inheritDoc
     */
    public function chunk(int $length, bool $preserve_keys = true): static
    {
        if ($length <= 0) {
            return new static();
        }

        $chunks = [];

        /** @var array<($preserve_keys is true ? TKey : int),TValue> $chunk */
        foreach (array_chunk($this->items, $length, $preserve_keys) as $chunk) {
            $chunks[] = new static($chunk);
        }

        return new static($chunks);
    }

    /**
     * Adds the given value to the start of the collection.
     *
     * @param TValue    $value
     *
     * @return static
     */
    public function prepend($value): self
    {
        array_unshift($this->items, $value);

        return $this;
    }

    /**
     * @inheritDoc
     *
     * @return static<TKey,TValue>
     */
    public function merge(mixed $values): Collection
    {
        $items = array_merge($this->items, $this->wrapArrayableItems($values));

        return Collection::make($items);
    }

    /**
     * @inheritDoc
     *
     * @return static<TKey,TValue>
     */
    public function mergeRecursive(mixed $values): Collection
    {
        $items = array_merge_recursive($this->items, $this->wrapArrayableItems($values));

        return Collection::make($items);
    }

    /**
     * @inheritDoc
     *
     * @return self<TKey,TValue>
     */
    public function push($key, $value = null): self
    {
        if ($value) {
            $this->items[$key] = $value;
        } else {
            $this->items[] = $key;
        }

        return $this;
    }

    /**
     * @template TNewValue
     *
     * @inheritDoc
     *
     * @return static<TKey,TNewValue>
     */
    public function map(callable $callback): Collection
    {
        /** @var array<TKey,TNewValue> $values */
        $values = array_map($callback, array_values($this->items), array_keys($this->items));

        return Collection::make($values);
    }

    /**
     * @inheritDoc
     *
     * @return self<TKey,TValue>
     */
    public function each(callable $callback): self
    {
        foreach ($this->items as $key => $value) {
            $callback($value, $key);
        }

        return $this;
    }

    /**
     * @inheritDoc
     *
     * @return static<int,mixed>
     */
    public function flatten(int $depth = PHP_INT_MAX): Collection
    {
        return Collection::make(array_flatten($this->items, $depth));
    }

    /**
     * @inheritDoc
     *
     * @return static<TKey,TValue>
     */
    public function filter(callable $callback): Collection
    {
        /** @var array<TKey,TValue> $values */
        $values = array_filter($this->items, $callback, ARRAY_FILTER_USE_BOTH);

        return Collection::make($values);
    }

    /**
     * @inheritDoc
     *
     * @return static<TKey,TValue>
     */
    public function keyBy(string $keyBy): Collection
    {
        $results = [];

        foreach ($this->items as $item) {
            // @phpstan-ignore offsetAccess.notFound
            $key = is_array($item) ? $item[$keyBy] : $item->$keyBy;

            $results[$key] = $item;
        }

        return new static($results);
    }

    /**
     * Groups all the items in the array by a field in their values.
     *
     * @param string|callable(TValue):array-key     $key
     *
     * @return static<array-key,array<TKey,TValue>>
     */
    public function groupBy(string|callable $key): Collection
    {
        $groups = [];

        $keyResolver = is_string($key)
            ? fn(mixed $item) => is_array($item) ? $item[$key] : $item->$key
            : $key;

        foreach ($this->items as $key => $item) {
            // @phpstan-ignore offsetAccess.notFound
            $groupKey = $keyResolver($item);

            $groups[$groupKey] ??= [];
            $groups[$groupKey][$key] = $item;
        }

        return new static($groups);
    }

    /**
     * @inheritDoc
     *
     * @return static<TKey,TValue>
     */
    public function sortBy(callable|string $callback, ?int $flags = null): Collection
    {
        if (is_string($callback)) {
            $field = $callback;

            $callback = function (mixed $value1, mixed $value2) use ($field, $flags): int {
                $value1 = $value1->{$field};
                $value2 = $value2->{$field};

                if ($flags & SORT_FLAG_CASE && is_string($value1) && is_string($value2)) {
                    $value1 = strtolower($value1);
                    $value2 = strtolower($value2);
                }

                if ($flags & SORT_NATURAL && is_string($value1) && is_string($value2)) {
                    return strnatcmp($value1, $value2);
                }

                if ($flags & SORT_NUMERIC && is_numeric($value1) && is_numeric($value2)) {
                    return $value2 - $value1;
                }

                return $value1 <=> $value2;
            };
        }

        /** @var array<TKey,TValue> $values */
        $values = [...$this->items];

        usort($values, $callback);

        return new static($values);
    }

    /**
     * @inheritDoc
     *
     * @return static<TKey,TValue>
     */
    public function sortKeys(): Collection
    {
        $values = [...$this->items];

        ksort($values);

        return new static($values);
    }

    /**
     * @inheritDoc
     *
     * @return static<($value is null ? int : string),mixed>
     */
    public function pluck(string $keyOrValue, null|string $value = null): self
    {
        if (is_null($value)) {
            [$key, $value] = [null, $keyOrValue];
        } else {
            $key = $keyOrValue;
        }

        $results = [];

        foreach ($this->items as $item) {
            if (!is_null($key)) {
                $results[$item[$key]] = $item[$value];
            } else {
                $results[] = $item[$value];
            }
        }

        return new static($results);
    }

    /**
     * @inheritDoc
     */
    public function dump(): void
    {
        print_r($this->items);
    }

    /**
     * @inheritDoc
     */
    public function join(string $separator = ""): string
    {
        return join($separator, $this->items);
    }

    /**
     * Get an iterator for the items.
     *
     * @return \ArrayIterator<TKey,TValue>
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->items);
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($key): bool
    {
        return isset($this->items[$key]);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($key): mixed
    {
        return $this->items[$key] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($key, $value): void
    {
        $this->items[$key] = $value;
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($key): void
    {
        unset($this->items[$key]);
    }

    /**
     * Gets the collection as an array.
     *
     * @return array<TKey,TValue>
     */
    public function toArray(): array
    {
        return $this->items;
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        return json_encode($this->toArray());
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->render();
    }

    /**
     * Gets all the items within the given values and attempts to wrap them in an array.
     *
     * @param   mixed   $items
     *
     * @return array<TKey, TValue>
     */
    public function wrapArrayableItems($items): array
    {
        if (is_array($items)) {
            return $items;
        }

        return match (true) {
            $items instanceof Collection => $items->toArray(),
            $items instanceof Arrayable => $items->toArray(),
            $items instanceof \Traversable => iterator_to_array($items),
            $items instanceof \JsonSerializable => (array) $items->jsonSerialize(),
            $items instanceof \UnitEnum => [$items],
            default => (array) $items,
        };
    }
}
