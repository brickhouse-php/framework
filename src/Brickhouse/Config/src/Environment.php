<?php

namespace Brickhouse\Config;

final class Environment
{
    private static string $current;

    /**
     * Get the current application environment.
     *
     * @return string
     */
    public static function current(): string
    {
        static::$current ??= env('APP_ENV', 'development');

        return strtolower(static::$current);
    }

    /**
     * Gets whether the current environment matches any of the given environments.
     *
     * @param string|string[] $env
     *
     * @return boolean
     */
    public static function is(string|array $env): bool
    {
        return array_any(
            array_wrap($env),
            fn(string $env) => strcasecmp($env, static::current()) === 0
        );
    }

    /**
     * Gets whether the current environment is 'production' or 'staging'.
     *
     * @return boolean
     */
    public static function isProduction(): bool
    {
        return self::is(['production', 'staging']);
    }

    /**
     * Gets the currently loaded environment file, if any.
     *
     * @return null|string
     */
    public static function file(): ?string
    {
        $basePath = app()->basePath;
        $currentEnv = self::current();

        $applicableEnvironmentFiles = [
            ".env.{$currentEnv}",
            ".env",
        ];

        foreach ($applicableEnvironmentFiles as $environmentFileName) {
            $environmentFilePath = path($basePath, $environmentFileName);

            if (file_exists($environmentFilePath)) {
                return $environmentFilePath;
            }
        }

        return null;
    }
}
