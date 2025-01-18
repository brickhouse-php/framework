<?php

namespace Brickhouse\View;

use Brickhouse\Support\Renderable;

class View implements Renderable
{
    /**
     * @param string                $alias
     * @param string                $path
     * @param array<string,mixed>   $data
     * @param string                $template
     */
    public function __construct(
        public readonly string $alias,
        public readonly string $path,
        public readonly array $data,
        public readonly string $template,
    ) {}

    /**
     * Creates a new `View`-instance from the given alias.
     *
     * @param string                $alias  Alias of the view.
     * @param array<string,mixed>   $data   Optional data to pass to the view.
     *
     * @return View
     */
    public static function fromAlias(string $alias, array $data = []): View
    {
        $renderer = resolve(Renderer::class);
        $absolutePath = $renderer->viewResolver->resolveView($alias);

        $template = $renderer->renderFile($absolutePath, $data);

        return new View($alias, $absolutePath, $data, $template);
    }

    /**
     * Creates a new `View`-instance which is named after the same alias, but potentially a different extension.
     *
     * @param string                $alias  Alias of the view.
     * @param array<string,mixed>   $data   Optional data to pass to the view.
     *
     * @return null|View
     */
    public static function findFallback(string $alias, array $data = []): null|View
    {
        // Strip the extension from the view alias, if one was given.
        if (str_contains(pathinfo($alias, PATHINFO_FILENAME), ".")) {
            $alias = preg_replace("/\.\w+\.php$/", "", $alias, limit: 1);
        }

        $candidates = glob(view_path($alias . '.*.php'));
        if (empty($candidates)) {
            return null;
        }

        // The `view` function needs the path to be relative, so we must replace the absolute path
        // with a relative one.
        $relativeViewPath = basename($candidates[0]);

        // If a directory was named in `$alias`, prepend it onto the relative path.
        if (($directory = pathinfo($alias, PATHINFO_DIRNAME))) {
            $relativeViewPath = $directory . DIRECTORY_SEPARATOR . $relativeViewPath;
        }

        return view($relativeViewPath, $data);
    }

    /**
     * Render the view into HTML.
     *
     * @return string
     */
    public function render(): string
    {
        return $this->template;
    }

    /**
     * Renders the template without compiling the template first.
     *
     * @return mixed
     */
    public function require(): mixed
    {
        $require = static function (string $path, array $data = []) {
            extract($data, EXTR_OVERWRITE);
            return require $path;
        };

        return $require($this->path, $this->data);
    }
}
