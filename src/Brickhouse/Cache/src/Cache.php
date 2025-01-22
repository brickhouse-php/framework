<?php

namespace Brickhouse\Cache;

/**
 * @mixin CacheProvider
 */
class Cache
{
    // @phpstan-ignore property.unusedType
    private static null|CacheConfig $config;

    /**
     * Gets the cache provider the with the given name.
     * If `$name` is `null`, returns the default cache provider instance.
     *
     * @param null|string   $name
     *
     * @return CacheProvider
     */
    public static function store(null|string $name = null): CacheProvider
    {
        $name ??= self::config()->default;

        return self::config()->providers[$name];
    }

    /**
     * Method overloading for calling caching methods on the default cache provider.
     *
     * @param string                    $name       Name of the method being called.
     * @param array<array-key,mixed>    $arguments  Optional arguments to the method.
     *
     * @return mixed
     */
    public static function __callStatic(string $name, array $arguments): mixed
    {
        $provider = self::store();

        if (method_exists($provider, $name)) {
            return $provider->$name(...$arguments);
        }

        throw new \InvalidArgumentException("Invalid cache method: {$name}");
    }

    /**
     * Gets the current `CacheConfig`-instance.
     *
     * @return CacheConfig
     */
    protected static function config(): CacheConfig
    {
        self::$config ??= resolve(CacheConfig::class);

        return self::$config;
    }
}
