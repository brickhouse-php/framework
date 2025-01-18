<?php

namespace Brickhouse\Core\Console\Commands;

use Brickhouse\Console\Attributes\Option;
use Brickhouse\Console\GeneratorCommand;
use Brickhouse\Console\InputOption;
use Brickhouse\Support\StringHelper;

class ModelGenerator extends GeneratorCommand
{
    /**
     * The type of the class generated.
     *
     * @var string
     */
    public string $type = 'Model';

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
     * Defines whether to scaffold a migration for the model.
     *
     * @var bool
     */
    #[Option("migration", null, "Whether to scaffold a migration for the model.", InputOption::NEGATABLE)]
    public bool $addMigration = true;

    /**
     * @inheritDoc
     */
    public function stub(): string
    {
        return __DIR__ . '/../../Stubs/Model.stub.php';
    }

    /**
     * @inheritDoc
     */
    public function handle(): int
    {
        $result = parent::handle();

        $this->createMigration();

        return $result;
    }

    /**
     * @inheritDoc
     */
    protected function defaultNamespace(string $rootNamespace): string
    {
        return $rootNamespace . 'Models';
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
            'name' => StringHelper::from($this->className)->lower()->__toString(),
            '--model',
            $this->className,
            $this->force ? '--force' : '--no-force'
        ]);
    }
}
