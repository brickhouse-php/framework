<?php

if (! function_exists("array_pull")) {
    /**
     * Pull the given key from the array and remove it.
     *
     * @template T
     * @template TReturn
     *
     * @param T[]                           $array
     * @param string|int                    $key
     * @param callable():TReturn|TReturn    $default
     *
     * @return T|TReturn|null
     */
    function array_pull(array &$array, string|int $key, $default = null): mixed
    {
        if (array_key_exists($key, $array)) {
            $value = $array[$key];
            unset($array[$key]);

            return $value;
        }

        if (is_callable($default)) {
            return $default();
        }

        return $default;
    }
}

if (! function_exists("array_flatten")) {
    /**
     * Flatten a multi-dimensional collection into a single level.
     *
     * @template T
     *
     * @param T[]   $array
     * @param int   $depth
     *
     * @return array<int,T>
     */
    function array_flatten(array $array, int $depth = PHP_INT_MAX): array
    {
        $result = [];

        foreach ($array as $item) {
            $item = $item instanceof \Brickhouse\Support\Collection
                ? $item->toArray()
                : $item;

            if (! is_array($item)) {
                $result[] = $item;
            } else {
                $values = $depth === 1
                    ? array_values($item)
                    : array_flatten($item, $depth - 1);

                foreach ($values as $value) {
                    $result[] = $value;
                }
            }
        }

        return $result;
    }
}

if (! function_exists("array_next_key")) {
    /**
     * Gets the key after the given key in the array.
     *
     * @template TKey of array-key
     *
     * @param array<TKey,*>                 $array
     * @param TKey                          $key
     * @param bool                          $wrap
     *
     * @return ($wrap is true ? TKey : (TKey|null))
     */
    function array_next_key(array $array, string|int $key, bool $wrap = true)
    {
        foreach (array_keys($array) as $choice) {
            if ($key !== $choice) {
                next($array);
                continue;
            }

            if (next($array) === false) {
                if ($wrap) {
                    reset($array);
                } else {
                    return null;
                }
            }

            return key($array);
        }

        return null;
    }
}

if (! function_exists("array_prev_key")) {
    /**
     * Gets the key before the given key in the array.
     *
     * @template TKey of array-key
     *
     * @param array<TKey,*>                 $array
     * @param TKey                          $key
     * @param bool                          $wrap
     *
     * @return ($wrap is true ? TKey : (TKey|null))
     */
    function array_prev_key(array $array, string|int $key, bool $wrap = true)
    {
        foreach (array_keys($array) as $choice) {
            if ($key !== $choice) {
                next($array);
                continue;
            }

            if (prev($array) === false) {
                if ($wrap) {
                    end($array);
                } else {
                    return null;
                }
            }

            return key($array);
        }

        return null;
    }
}

if (! function_exists("array_wrap")) {
    /**
     * Wrap the given values in a new array.
     *
     * @template T
     *
     * @param T|T[]     $value
     *
     * @return array<int,T>
     */
    function array_wrap($value): array
    {
        if (is_array($value)) {
            return $value;
        }

        return [$value];
    }
}

if (! function_exists('class_uses_recursive')) {
    /**
     * Returns all traits used by a class, its parent classes and trait of their traits.
     *
     * @param  object|class-string  $class
     *
     * @return list<class-string>
     */
    function class_uses_recursive(object|string $class): array
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        $results = [];

        foreach (array_reverse(class_parents($class) ?: []) + [$class => $class] as $class) {
            $results += trait_uses_recursive($class);
        }

        return array_unique($results);
    }
}

if (! function_exists('trait_uses_recursive')) {
    /**
     * Returns all traits used by a trait and its traits.
     *
     * @param  object|class-string      $trait
     *
     * @return list<class-string>
     */
    function trait_uses_recursive(object|string $trait): array
    {
        $traits = class_uses($trait);
        if ($traits === false) {
            $traits = [];
        }

        foreach ($traits as $trait) {
            $traits += trait_uses_recursive($trait);
        }

        return $traits;
    }
}

if (! function_exists("has_trait")) {
    /**
     * Checks whether the given object or class uses the given trait.
     *
     * @param class-string|object   $object_or_class    Object or class name to check for.
     * @param class-string          $trait              Trait class name to check against.
     *
     * @return bool
     */
    function has_trait(string|object $object_or_class, string $trait): bool
    {
        return in_array($trait, class_uses_recursive($object_or_class));
    }
}

if (! function_exists("random_byte_string")) {
    /**
     * Creates a hexadecimal string with random bytes.
     *
     * @param int   $length     The length of the byte string.
     *
     * @return string
     */
    function random_byte_string(int $length = 16): string
    {
        // Since each byte takes up 2 characters in the string, we half the given length.
        $byteLength = (int) ceil($length / 2);

        $byteString = bin2hex(random_bytes($byteLength));

        // In case an odd number was given, trim the string to be `$length` characters long.
        return substr($byteString, 0, $length);
    }
}

if (!function_exists("encrypt")) {
    /**
     * Encrypts the given value with the AES-256-GCM cipher.
     *
     * @param mixed     $value          Value to encrypt.
     * @param bool      $serialize      Whether to serialize the value before encrypting.
     *
     * @return string
     */
    function encrypt(mixed $value, bool $serialize = true): string
    {
        return new \Brickhouse\Support\Crypto()->encrypt(...func_get_args());
    }
}

if (!function_exists("decrypt")) {
    /**
     * Decrypts the given value with the AES-256-GCM cipher.
     *
     * @param string    $value          Cipter-text to decrypt.
     * @param bool      $deserialize    Whether to deserialize the value before decrypting.
     *
     * @return mixed
     */
    function decrypt(string $value, bool $deserialize = true): mixed
    {
        return new \Brickhouse\Support\Crypto()->decrypt(...func_get_args());
    }
}

if (! function_exists("path")) {
    /**
     * Join all the given path segments into a single path.
     *
     * @param string    $segments
     *
     * @return string
     */
    function path(string ...$segments): string
    {
        return preg_replace('#/+#', '/', join('/', $segments));
    }
}

if (! function_exists("tap")) {
    /**
     * @template TInput
     *
     * @param TInput                            $input
     * @param callable(TInput $input): mixed    $callback
     *
     * @return TInput
     */
    function tap($input, callable $callback)
    {
        $callback($input);

        return $input;
    }
}

if (! function_exists("when")) {
    /**
     * Returns the result of `$truthy` if the given condition is `true`.
     * Otherwise, if `$falsy` is specified, returns the result of `$falsy`.
     * If `$falsy` is not specified, returns `null`.
     *
     * @template TTruthy
     * @template TFalsy
     *
     * @param bool                          $condition
     * @param TTruthy|(pure-callable():TTruthy)  $truthy
     * @param TFalsy|(pure-callable():TFalsy)    $falsy
     *
     * @return ($falsy is null ? TTruthy|null : TTruthy|TFalsy)
     */
    function when(bool $condition, $truthy, $falsy)
    {
        $evaluate = function ($callback) {
            if (is_null($callback)) {
                return null;
            }

            if (is_callable($callback)) {
                return $callback();
            }

            return $callback;
        };

        return $condition
            ? $evaluate($truthy)
            : $evaluate($falsy);
    }
}
