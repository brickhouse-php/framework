<?php

namespace Brickhouse\Cache;

interface CacheProvider
{
    /**
     * Determines whether the given cache key currently exists in the cache.
     *
     * @return bool `true` on success and `false` on failure.
     */
    public function has(string $key): bool;

    /**
     * Retrieves the item from the cache with the given cache key, if it exists within the cache. Otherwise, returns `$default`.
     *
     * @template TItem
     *
     * @param string        $key        Cache key for the item to retrieve.
     * @param TItem|null    $default    Defines a default value to return if the cached item wasn't found.
     *
     * @return TItem|null
     */
    public function get(string $key, $default = null);

    /**
     * Atomically attemps to retrieve the given cache key from the cache. If it exists, it is returned. Otherwise,
     * a new cache value is created from `$generator`, which will then be cached.
     *
     * @template TItem
     *
     * @param string                        $key        Cache key for the item to retrieve.
     * @param callable(string $key):TItem   $generator  A callable which accepts the cache key and returns the value to cache.
     * @param null|int|\DateInterval        $ttl        Optional. The TTL (time-to-live) value of this item in seconds.
     *                                                  If `null`, the provider may set a default value or only delete the value from the cache when needed.
     *
     * @return TItem
     */
    public function getOrElse(string $key, callable $generator, null|int|\DateInterval $ttl = null);

    /**
     * Persists the given value in the cache with the given cache key. Optionally, add an expiration to the cached value.
     *
     * @param string                    $key        Cache key to identify the cached value.
     * @param mixed                     $value      Value to cache in the cache provider.
     * @param null|int|\DateInterval    $ttl        Optional. The TTL (time-to-live) value of this item in seconds.
     *                                              If `null`, the provider may set a default value or only delete the value from the cache when needed.
     *
     * @return bool `true` on success and `false` on failure.
     */
    public function set(string $key, mixed $value, null|int|\DateInterval $ttl = null): bool;

    /**
     * Deletes an item from the cache from it's cache key.
     *
     * @param string        $key        Cache key of the cached value to remove.
     *
     * @return bool `true` on success and `false` on failure.
     */
    public function delete(string $key): bool;

    /**
     * Deletes all the items from the cache.
     *
     * @return bool `true` on success and `false` on failure.
     */
    public function clear(): bool;
}
