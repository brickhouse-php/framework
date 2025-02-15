<?php

namespace Brickhouse\View\Commands;

use Brickhouse\Console\Attributes\Argument;
use Brickhouse\Console\Attributes\Option;
use Brickhouse\Console\GeneratorCommand;
use Brickhouse\Console\InputOption;
use Brickhouse\Support\StringHelper;

class ViewGenerator extends GeneratorCommand
{
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

    /**
     * Defines the name of the generated view.
     *
     * @var string
     */
    #[Argument("name", "Specifies the name of the view", InputOption::REQUIRED)]
    public string $viewName = '';

    /**
     * @inheritDoc
     */
    protected function sourceRoot(): string
    {
        return __DIR__ . '/../Stubs/';
    }

    /**
     * @inheritDoc
     */
    public function handle(): int
    {
        $this->viewName = StringHelper::from($this->viewName)
            ->lower()
            ->__toString();

        $ext = $this->json ? '.php.json' : '.php.html';
        $stub = "View.stub{$ext}";

        $this->copy(
            $stub,
            path('resources', 'views', $this->viewName . $ext),
            [
                ...$this->getControllerAction(),
            ]
        );

        return 0;
    }

    /**
     * Determines the name of the controller and action for the view.
     *
     * @return array{controller:string,action:string}
     */
    protected function getControllerAction(): array
    {
        $segments = explode("/", $this->viewName);
        $controllerName = $segments[0];
        $actionName = $segments[1] ?? null;

        if ($actionName === null) {
            $actionName = $controllerName;
            $controllerName = 'index';
        }

        $controllerName = StringHelper::from($controllerName)
            ->end("Controller")
            ->capitalize();

        return [
            'controller' => $controllerName,
            'action' => $actionName
        ];
    }
}
