<?php

namespace Brickhouse\View;

use Brickhouse\View\Engine\Compiler;

class Renderer extends \Brickhouse\View\Engine\Renderer
{
    /**
     * Contains an internal cache of compiled templates.
     *
     * @var array<string,string>
     */
    protected array $compiledCache = [];

    public function __construct()
    {
        parent::__construct(
            new ViewResolver(),
            new Compiler(),
        );
    }

    /**
     * Render the given template content into a fully-rendered HTML document.
     *
     * @param string                $template       Template content to render.
     * @param array<string,mixed>   $data           Optional data to pass to the template.
     *
     * @return string
     */
    public function render(string $template, array $data = []): string
    {
        $compiled = $this->compileTemplate($template);
        $data = [
            ...$data,
            '__renderer' => $this,
            '__fragment' => end($this->fragmentRenderStack),
        ];

        return $this->renderCompiledTemplate($compiled, $data);
    }

    /**
     * Computes a hash key for the given template string.
     *
     * @param string $template
     *
     * @return string
     */
    protected function computeHashKey(string $template): string
    {
        return 'view-' . hash('xxh128', $template, binary: false);
    }

    /**
     * Compiles the given template into a compiled HTML template, which can be rendered.
     *
     * @param string $template
     *
     * @return string
     */
    protected function compileTemplate(string $template): string
    {
        $hashKey = $this->computeHashKey($template);

        if (isset($this->compiledCache[$hashKey])) {
            return $this->compiledCache[$hashKey];
        }

        $compiled = $this->compiledCache[$hashKey] = $this->compiler->compile($template);

        return $compiled;
    }
}
