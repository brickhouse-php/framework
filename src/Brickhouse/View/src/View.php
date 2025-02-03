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
        public array $data,
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
