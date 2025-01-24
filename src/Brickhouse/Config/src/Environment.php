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

        return static::$current;
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
        $env = array_wrap($env);

        return in_array(static::current(), $env);
    }

    /**
     * Gets whether the current environment is 'production'.
     *
     * @return boolean
     */
    public static function isProduction(): bool
    {
        return strtolower(static::$current) === 'production';
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
