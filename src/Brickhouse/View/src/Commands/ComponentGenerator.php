<?php

namespace Brickhouse\View\Commands;

use Brickhouse\Console\GeneratorCommand;
use Brickhouse\Support\StringHelper;

class ComponentGenerator extends GeneratorCommand
{
    /**
     * The type of the class generated.
     *
     * @var string
     */
    public string $type = 'Component';

    /**
     * The name of the console command.
     *
     * @var string
     */
    public string $name = 'generate:component';

    /**
     * The description of the console command.
     *
     * @var string
     */
    public string $description = 'Scaffolds a new component.';

    public function stub(): string
    {
        return __DIR__ . '/../Stubs/Component.stub.php.html';
    }

    protected function defaultNamespace(string $rootNamespace): string
    {
        return $rootNamespace . 'Views\\Components';
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
        return component_path(strtolower($name) . '.php.html');
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
            ["ComponentName"],
            [StringHelper::from(end($segments))->snake('-')],
            $content
        );

        return $content;
    }
}
