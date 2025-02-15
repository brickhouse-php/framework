<?php

namespace Brickhouse\Core\Console\Commands;

use Brickhouse\Console\Attributes\Argument;
use Brickhouse\Console\Attributes\Option;
use Brickhouse\Console\GeneratorCommand;
use Brickhouse\Console\InputOption;
use Brickhouse\Support\StringHelper;

class ModelGenerator extends GeneratorCommand
{
    /**
     * The name of the console command.
     *
     * @var string
     */
    public string $name = 'generate:model';

    /**
     * The description of the console command.
     *
     * @var string
     */
    public string $description = 'Scaffolds a new database model.';

    /**
     * Defines the name of the generated model.
     *
     * @var string
     */
    #[Argument("name", "Specifies the name of the model", InputOption::REQUIRED)]
    public string $modelName = '';

    /**
     * Defines whether to scaffold a migration for the model.
     *
     * @var bool
     */
    #[Option("migration", null, "Whether to scaffold a migration for the model.", InputOption::NEGATABLE)]
    public bool $addMigration = true;

    /**
     * @inheritDoc
     */
    protected function sourceRoot(): string
    {
        return __DIR__ . '/../../Stubs/';
    }

    /**
     * @inheritDoc
     */
    public function handle(): int
    {
        $this->copy(
            'Model.stub.php',
            path('src', 'Models', $this->modelName . '.php'),
            [
                'modelNamespace' => 'App\\Models',
                'modelClass' => $this->modelName,
            ]
        );

        $this->createMigration();

        return 0;
    }

    /**
     * Creates the corresponding migration for the model, if requested.
     *
     * @return void
     */
    protected function createMigration(): void
    {
        if (!$this->addMigration) {
            return;
        }

        $this->call('generate:migration', [
            'name' => StringHelper::from($this->modelName)->lower()->__toString(),
            '--model',
            $this->modelName,
            $this->force ? '--force' : '--no-force'
        ]);
    }
}
