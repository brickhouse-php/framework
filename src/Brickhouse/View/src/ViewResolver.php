<?php

namespace Brickhouse\View;

use \Brickhouse\View\Engine\ViewResolver as BaseViewResolver;

class ViewResolver extends BaseViewResolver
{
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

        if (pathinfo($path, PATHINFO_EXTENSION) === '') {
            $path .= BaseViewResolver::EXTENSION;
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

        if (pathinfo($path, PATHINFO_EXTENSION) === '') {
            $path .= BaseViewResolver::EXTENSION;
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
        $alias = substr($alias, strlen(BaseViewResolver::ALIAS_PREFIX));

        $path = ltrim($alias, '/\\');
        $path = str_replace(['/', '\\'], ['/', '/'], $path);

        if (pathinfo($path, PATHINFO_EXTENSION) === '') {
            $path .= BaseViewResolver::EXTENSION;
        }

        return component_path($path);
    }
}
