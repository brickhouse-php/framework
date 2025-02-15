<?php

namespace Brickhouse\View\Commands;

use Brickhouse\Console\Attributes\Argument;
use Brickhouse\Console\GeneratorCommand;
use Brickhouse\Console\InputOption;
use Brickhouse\Support\StringHelper;

class ComponentGenerator extends GeneratorCommand
{
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

    /**
     * Defines the name of the generated component.
     *
     * @var string
     */
    #[Argument("name", "Specifies the name of the component", InputOption::REQUIRED)]
    public string $componentName = '';

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
        $this->componentName = StringHelper::from($this->componentName)
            ->capitalize()
            ->__toString();

        $this->copy(
            'Component.stub.php',
            path('resources', 'views', 'components', $this->componentName . '.php.html'),
            [
                'componentName' => $this->componentName,
            ]
        );

        return 0;
    }
}
