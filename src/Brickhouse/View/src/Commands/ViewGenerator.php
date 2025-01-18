<?php

namespace Brickhouse\View\Commands;

use Brickhouse\Console\Attributes\Option;
use Brickhouse\Console\GeneratorCommand;
use Brickhouse\Console\InputOption;
use Brickhouse\Support\StringHelper;

class ViewGenerator extends GeneratorCommand
{
    /**
     * The type of the class generated.
     *
     * @var string
     */
    public string $type = 'View';

    /**
     * The name of the console command.
     *
     * @var string
     */
    public string $name = 'generate:view';

    /**
     * The description of the console command.
     *
     * @var string
     */
    public string $description = 'Creates a new view.';

    /**
     * Defines whether to create a JSON view instead of an HTML view.
     *
     * @var bool
     */
    #[Option("json", null, "Create a JSON view instead of an HTML view.", InputOption::NEGATABLE)]
    public bool $json = false;

    public function stub(): string
    {
        if ($this->json) {
            return __DIR__ . '/../Stubs/View.stub.json.php';
        }

        return __DIR__ . '/../Stubs/View.stub.html.php';
    }

    protected function defaultNamespace(string $rootNamespace): string
    {
        return $rootNamespace . 'Views';
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
        $extension = $this->json ? '.json.php' : '.html.php';

        return view_path($name . $extension);
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
        $controllerName = $segments[0];
        $actionName = $segments[1] ?? null;

        if ($actionName === null) {
            $actionName = $controllerName;
            $controllerName = 'index';
        }

        $content = str_replace(
            [
                "ControllerName",
                "ActionName",
            ],
            [
                StringHelper::from($controllerName)->end("Controller")->capitalize(),
                $actionName,
            ],
            $content
        );

        return $content;
    }
}
