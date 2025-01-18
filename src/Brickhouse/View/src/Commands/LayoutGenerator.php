<?php

namespace Brickhouse\View\Commands;

use Brickhouse\Console\GeneratorCommand;
use Brickhouse\Support\StringHelper;

class LayoutGenerator extends GeneratorCommand
{
    /**
     * The type of the class generated.
     *
     * @var string
     */
    public string $type = 'Layout';

    /**
     * The name of the console command.
     *
     * @var string
     */
    public string $name = 'generate:layout';

    /**
     * The description of the console command.
     *
     * @var string
     */
    public string $description = 'Creates a new layout.';

    public function stub(): string
    {
        return __DIR__ . '/../Stubs/Layout.stub.html.php';
    }

    protected function defaultNamespace(string $rootNamespace): string
    {
        return $rootNamespace . 'Views\\Layouts';
    }

    /**
     * Gets the destination class path for the given class.
     *
     * @param  string  $name
     *
     * @return string
     */
    protected function getPath(string $name): string
    {
        return layout_path($name . '.html.php');
    }

    /**
     * Builds the content of the stub.
     *
     * @return string
     */
    protected function buildStub(string $path, string $name): string
    {
        $content = parent::buildStub($path, $name);

        $segments = explode("/", $name);

        $content = str_replace(
            ["LayoutName"],
            [StringHelper::from(end($segments))->title()],
            $content
        );

        return $content;
    }
}
