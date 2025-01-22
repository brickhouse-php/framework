<?php

use Brickhouse\Core\Application;

if (!function_exists("app")) {
    /**
     * Gets the current application instance.
     *
     * @return Application
     */
    function app(): Application
    {
        return Application::current();
    }
}

if (!function_exists("base_path")) {
    /**
     * Gets the applications root directory.
     *
     * @param string    $path   Optional path to append to the base path.
     *
     * @return string
     */
    function base_path(?string ...$path): string
    {
        return path(app()->basePath, ...$path);
    }
}

if (!function_exists("app_path")) {
    /**
     * Gets the applications root directory.
     *
     * @param string    $path   Optional path to append to the application path.
     *
     * @return string
     */
    function app_path(?string ...$path): string
    {
        return path(app()->appPath, ...$path);
    }
}

if (!function_exists("build_path")) {
    /**
     * Gets the applications build directory.
     *
     * @param string    $path   Optional path to append to the build path.
     *
     * @return string
     */
    function build_path(?string ...$path): string
    {
        return public_path('_build', ...$path);
    }
}

if (!function_exists("config_path")) {
    /**
     * Gets the applications configuration directory.
     *
     * @param string    $path   Optional path to append to the configuration path.
     *
     * @return string
     */
    function config_path(?string ...$path): string
    {
        return path(app()->configPath, ...$path);
    }
}

if (!function_exists("storage_path")) {
    /**
     * Gets the applications storage directory.
     *
     * @param string    $path   Optional path to append to the storage path.
     *
     * @return string
     */
    function storage_path(?string ...$path): string
    {
        return path(app()->storagePath, ...$path);
    }
}

if (!function_exists("public_path")) {
    /**
     * Gets the applications public directory.
     *
     * @param string    $path   Optional path to append to the public path.
     *
     * @return string
     */
    function public_path(?string ...$path): string
    {
        return path(app()->publicPath, ...$path);
    }
}

if (!function_exists("resolve")) {
    /**
     * Resolves an instance of the given type from the service container.
     *
     * @template T
     *
     * @param string|class-string<T> $abstract
     * @param array<array-key,mixed> $parameters
     *
     * @return ($abstract is class-string<T> ? T : mixed)
     */
    function resolve(string $abstract, array $parameters = [])
    {
        return app()->resolve($abstract, $parameters);
    }
}
