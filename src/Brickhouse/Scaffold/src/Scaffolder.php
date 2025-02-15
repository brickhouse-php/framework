<?php

namespace Brickhouse\Scaffold;

use Handlebars\Handlebars;
use Handlebars\Loader\StringLoader;

class Scaffolder
{
    /**
     * Defines the Handlebars renderer.
     *
     * @var Handlebars
     */
    protected readonly Handlebars $handlebars;

    public function __construct(
        protected readonly string $templateRoot,
        protected readonly string $destinationRoot,
    ) {
        $loader = new StringLoader();

        $this->handlebars = new Handlebars([
            'loader' => $loader,
        ]);
    }

    /**
     * Creates a new stubbed file from the template file at the given path.
     *
     * @param string                $template       Handlebars template to render.
     * @param array<string,mixed>   $data           Optional data to pass to the template.
     *
     * @return void
     */
    public function stub(string $template, string $destination, array $data = []): void
    {
        $rendered = $this->renderTemplateFile($template, $data);

        $this->emplaceFileContent($destination, $rendered);
    }

    /**
     * Creates a new stubbed file from the given template string.
     *
     * @param string                $path           Path to the Handlebars template to render.
     * @param array<string,mixed>   $data           Optional data to pass to the template.
     *
     * @return void
     */
    public function stubTemplate(string $template, string $destination, array $data = []): void
    {
        $rendered = $this->renderTemplate($template, $data);

        $this->emplaceFileContent($destination, $rendered);
    }

    /**
     * Renders the Handlebars template at the given path into it's rendered format.
     *
     * @param string                $path           Path to the Handlebars template file, relative to the template root.
     * @param array<string,mixed>   $data           Optional data to pass to the template.
     *
     * @return string
     */
    protected function renderTemplateFile(string $path, array $data = []): string
    {
        $path = path($this->templateRoot, $path);
        $template = @file_get_contents($path);

        if ($template === false) {
            throw new \InvalidArgumentException("Stub template could not be found: {$path}");
        }

        return $this->renderTemplate($template, $data);
    }

    /**
     * Renders the given Handlebars template into it's rendered format.
     *
     * @param string                $template       Handlebars template string to render.
     * @param array<string,mixed>   $data           Optional data to pass to the template.
     *
     * @return string
     */
    protected function renderTemplate(string $template, array $data = []): string
    {
        return $this->handlebars->render($template, $data);
    }

    /**
     * Writes the given content to the file at the given path.
     *
     * @param string    $path           Path to the file where to write the content. Relative to the destination root.
     * @param string    $content        Content to write to the given path.
     *
     * @return void
     *
     * @throws \RuntimeException        Thrown if writing the file failed.
     */
    protected function emplaceFileContent(string $path, string $content): void
    {
        $absolutePath = path($this->destinationRoot, $path);

        if (@file_put_contents($absolutePath, $content, LOCK_EX) === false) {
            $error = error_get_last()['message'] ?? 'unknown';

            throw new \RuntimeException("Failed to create stub file '{$path}': {$error}");
        }
    }
}
