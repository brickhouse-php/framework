<?php

namespace Brickhouse\Cache;

readonly class ApcuCache implements CacheProvider
{
    public function __construct()
    {
        if (!function_exists("apcu_enabled")) {
            throw new \RuntimeException("Cannot use APCu cache provider: APCu not available.");
        }

        if (!apcu_enabled()) {
            throw new \RuntimeException("Cannot use APCu cache provider: APCu not enabled.");
        }
    }

    /**
     * @inheritDoc
     */
    public function has(string $key): bool
    {
        return \apcu_exists($key);
    }

    /**
     * @inheritDoc
     */
    public function get(string $key, $default = null)
    {
        $value = \apcu_fetch($key, $success);
        if (!$success) {
            return $default;
        }

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function getOrElse(string $key, callable $generator, null|int|\DateInterval $ttl = null)
    {
        $ttl ??= 0;

        if ($ttl instanceof \DateInterval) {
            $ttl = $ttl->s;
        }

        return \apcu_entry($key, $generator, $ttl);
    }

    /**
     * @inheritDoc
     */
    public function set(string $key, mixed $value, null|int|\DateInterval $ttl = null): bool
    {
        $ttl ??= 0;

        if ($ttl instanceof \DateInterval) {
            $ttl = $ttl->s;
        }

        return \apcu_store($key, $value, $ttl);
    }

    /**
     * @inheritDoc
     */
    public function delete(string $key): bool
    {
        return \apcu_delete($key);
    }

    /**
     * @inheritDoc
     */
    public function clear(): bool
    {
        return \apcu_clear_cache();
    }
}
