<?php

namespace Brickhouse\Cache;

use Carbon\Carbon;
use DateInterval;

/**
 * @phpstan-type    CachedValue     array{value:mixed,expiration:null|Carbon}
 */
class ArrayCache implements CacheProvider
{
    /**
     * Defines all the values in the cache.
     *
     * @var array<string,CachedValue>
     */
    protected array $values = [];

    /**
     * @inheritDoc
     */
    public function has(string $key): bool
    {
        $value = $this->values[$key] ?? null;

        if ($value === null) {
            return false;
        }

        if ($this->isExpired($value)) {
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function get(string $key, $default = null)
    {
        $value = $this->values[$key] ?? null;
        if ($value === null) {
            return $default;
        }

        if ($this->isExpired($value)) {
            unset($this->values[$key]);
            return null;
        }

        return $value['value'];
    }

    /**
     * @inheritDoc
     */
    public function getOrElse(string $key, callable $generator, null|int|\DateInterval $ttl = null)
    {
        $value = $this->values[$key] ?? null;

        if ($value !== null && $this->isExpired($value)) {
            $value = null;
        }

        if ($value === null) {
            $ttl = $this->getExpiration($ttl);
            $value = $generator($key);

            $this->values[$key] = ['value' => $value, 'expiration' => $ttl];

            return $value;
        }

        return $value['value'];
    }

    /**
     * @inheritDoc
     */
    public function set(string $key, mixed $value, null|int|\DateInterval $ttl = null): bool
    {
        $this->values[$key] = [
            'value' => $value,
            'expiration' => $this->getExpiration($ttl)
        ];

        return true;
    }

    /**
     * @inheritDoc
     */
    public function delete(string $key): bool
    {
        unset($this->values[$key]);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function clear(): bool
    {
        $this->values = [];

        return true;
    }

    /**
     * Gets whether the given cached value is expired or not.
     *
     * @param CachedValue $item
     *
     * @return bool
     */
    protected function isExpired(array $item): bool
    {
        if (($expiration = $item['expiration']) !== null && Carbon::now()->greaterThanOrEqualTo($expiration)) {
            return true;
        }

        return false;
    }

    /**
     * Gets the expiration from the given TTL.
     *
     * @param null|integer|DateInterval $ttl
     *
     * @return null|Carbon
     */
    protected function getExpiration(null|int|DateInterval $ttl): null|Carbon
    {
        if (is_int($ttl)) {
            $ttl = new DateInterval("PT{$ttl}S");
        }

        if ($ttl !== null) {
            $ttl = Carbon::now()->add($ttl);
        }

        return $ttl;
    }
}
