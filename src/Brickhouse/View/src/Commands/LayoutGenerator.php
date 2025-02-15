<?php

namespace Brickhouse\View\Commands;

use Brickhouse\Console\Attributes\Argument;
use Brickhouse\Console\GeneratorCommand;
use Brickhouse\Console\InputOption;
use Brickhouse\Support\StringHelper;

class LayoutGenerator extends GeneratorCommand
{
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

    /**
     * Defines the name of the generated layout.
     *
     * @var string
     */
    #[Argument("name", "Specifies the name of the layout", InputOption::REQUIRED)]
    public string $layoutName = '';

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
        $this->layoutName = StringHelper::from($this->layoutName)
            ->lower()
            ->__toString();

        $this->copy(
            'Component.stub.php',
            path('resources', 'views', 'layouts', $this->layoutName . '.php.html'),
            [
                'layoutName' => StringHelper::from($this->layoutName)->title(),
            ]
        );

        return 0;
    }
}
