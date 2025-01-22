<?php

namespace Brickhouse\View;

use Brickhouse\Cache\Cache;
use Brickhouse\Config\Environment;
use Brickhouse\View\Engine\Compiler;
use Brickhouse\View\Engine\Exceptions\ViewNotFoundException;

class Renderer extends \Brickhouse\View\Engine\Renderer
{
    public function __construct()
    {
        parent::__construct(
            new ViewResolver(),
            new Compiler(),
        );
    }

    /**
     * Render the template at the given file path into a fully-rendered HTML document.
     *
     * @param string                $path       Template file path to render.
     * @param array<string,mixed>   $data           Optional data to pass to the template.
     *
     * @return string
     */
    public function renderFile(string $path, array $data = []): string
    {
        $content = Cache::store('views')->getOrElse(
            realpath($path),
            fn() => @file_get_contents($path) ?: null
        );

        if (!$content) {
            throw new ViewNotFoundException($path);
        }

        return $this->render($content, $data);
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
        $data = [
            ...$data,
            '__renderer' => $this,
            '__fragment' => end($this->fragmentRenderStack),
        ];

        return $this->renderTemplate($template, $data);
    }

    /**
     * Render the given compiled template into a fully-rendered HTML document.
     *
     * @param string                $template       Template content to render.
     * @param array<string,mixed>   $data           Optional data to pass to the template.
     *
     * @return string
     */
    protected function renderTemplate(string $template, array $data): string
    {
        if (Environment::isProduction()) {
            return $this->renderTemplateAsProduction($template, $data);
        }

        $compiled = $this->compileTemplate($template);

        // Create a temporary file to store the template
        $compiledFilePath = tempnam(sys_get_temp_dir(), "brickhouse-view-");

        $compiledFile = fopen($compiledFilePath, "w");
        fwrite($compiledFile, $compiled);
        fclose($compiledFile);

        try {
            // Render the temporary file into HTML
            return $this->renderCompiledTemplateFile($compiledFilePath, $data);
        } catch (\Throwable $e) {
            throw $e;
        } finally {
            // Finally, delete the temporary file again.
            unlink($compiledFilePath);
        }
    }

    /**
     * Render the given compiled template into a fully-rendered HTML document in a production build.
     *
     * @param string                $template       Template content to render.
     * @param array<string,mixed>   $data           Optional data to pass to the template.
     *
     * @return string
     */
    private function renderTemplateAsProduction(string $template, array $data): string
    {
        $compiledFilePath = $this->prerenderTemplate($template);

        return $this->renderCompiledTemplateFile($compiledFilePath, $data);
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

        return Cache::store('views')->getOrElse(
            $hashKey,
            fn() => $this->compiler->compile($template)
        );
    }

    /**
     * Compiles the given template into a compiled HTML template and saves it for next application startup.
     *
     * @param string $template
     *
     * @return string       Absolute path to the pre-rendered template file.
     */
    public function prerenderTemplate(string $template): string
    {
        $compiled = $this->compileTemplate($template);

        $hashKey = $this->computeHashKey($template);
        $compiledFilePath = build_path('compiled', $hashKey . ".php");

        // Only write the file if it doesn't already exist.
        if (!file_exists($compiledFilePath)) {
            // Create the built view directory, if it doesn't already exist.
            @mkdir(dirname($compiledFilePath), recursive: true);

            $compiledFile = fopen($compiledFilePath, "w");
            fwrite($compiledFile, $compiled);
            fclose($compiledFile);

            if (function_exists('opcache_compile_file')) {
                \opcache_compile_file($compiledFilePath);
            }
        }

        return $compiledFilePath;
    }
}
