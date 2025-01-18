<?php

use Brickhouse\View\View;

if (!function_exists("resource_path")) {
    /**
     * Gets the applications resource directory.
     *
     * @param string    $path   Optional path to append to the resource path.
     *
     * @return string
     */
    function resource_path(?string ...$path): string
    {
        return base_path('resources', ...$path);
    }
}

if (!function_exists("view_path")) {
    /**
     * Gets the applications view directory.
     *
     * @param string    $path   Optional path to append to the view path.
     *
     * @return string
     */
    function view_path(?string ...$path): string
    {
        return resource_path('views', ...$path);
    }
}

if (!function_exists("component_path")) {
    /**
     * Gets the applications component directory.
     *
     * @param string    $path   Optional path to append to the component path.
     *
     * @return string
     */
    function component_path(?string ...$path): string
    {
        return view_path('components', ...$path);
    }
}

if (!function_exists("layout_path")) {
    /**
     * Gets the applications layout directory.
     *
     * @param string    $path   Optional path to append to the layout path.
     *
     * @return string
     */
    function layout_path(?string ...$path): string
    {
        return view_path('layouts', ...$path);
    }
}

if (!function_exists("view")) {
    /**
     * Create a new view from the given path.
     *
     * @param string                $path       Path to the view.
     * @param array<string,mixed>   $viewbag    Optional data to pass to the view.
     *
     * @return View
     */
    function view(string $path, array $viewbag = []): View
    {
        return View::fromAlias($path, $viewbag);
    }
}
