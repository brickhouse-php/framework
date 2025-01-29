<?php

namespace Brickhouse\View;

use \Brickhouse\View\Engine\ViewResolver as BaseViewResolver;

class ViewResolver extends BaseViewResolver
{
    /**
     * Gets the extension for supported views.
     *
     * @var string
     */
    public const string EXTENSION = ".php.html";

    public function __construct()
    {
        parent::__construct(base_path());
    }

    /**
     * Guesses the path of the view with the given alias.
     *
     * @param string $alias
     *
     * @return string
     */
    public function resolveView(string $alias): string
    {
        $path = ltrim($alias, '/\\');
        $path = str_replace(['/', '\\'], ['/', '/'], $path);

        if (!fnmatch('*.php.*', $alias)) {
            $path = str_replace('.', '/', $path);
            $path .= self::EXTENSION;
        }

        return view_path($path);
    }

    /**
     * Guesses the path of the layout with the given alias.
     *
     * @param string $alias
     *
     * @return string
     */
    public function resolveLayout(string $alias): string
    {
        // Strip layout alias prefix
        $alias = substr($alias, strlen(BaseViewResolver::LAYOUT_PREFIX));

        $path = ltrim($alias, '/\\');
        $path = str_replace(['/', '\\'], ['/', '/'], $path);

        if (!str_ends_with($alias, '.php')) {
            $path = str_replace('.', '/', $path);
            $path .= self::EXTENSION;
        }

        return layout_path($path);
    }

    /**
     * Guesses the path of the component with the given alias.
     *
     * @param string $alias
     *
     * @return string
     */
    public function resolveComponent(string $alias): string
    {
        // Strip fragment alias prefix
        $alias = substr($alias, strlen(self::ALIAS_PREFIX));

        $path = ltrim($alias, '/\\');
        $path = str_replace(['/', '\\'], ['/', '/'], $path);

        if (!str_ends_with($alias, '.php')) {
            $path = str_replace('.', '/', $path);
            $path .= self::EXTENSION;
        }

        return component_path($path);
    }
}
