<?php

namespace Brickhouse\Console;

use Brickhouse\Console\Attributes\Option;
use Brickhouse\Scaffold\Scaffolder;

abstract class GeneratorCommand extends Command
{
    /**
     * Defines the scaffolder for the generator.
     *
     * @var Scaffolder
     */
    private Scaffolder $scaffolder;

    /**
     * Defines whether to overwrite any existing files.
     *
     * @var bool
     */
    #[Option("force", "f", "Whether to override the existing file, if it exists", InputOption::NEGATABLE)]
    public bool $force = false;

    /**
     * Defines the root directory to search for stubbing templates.
     *
     * @return string
     */
    protected abstract function sourceRoot(): string;

    /**
     * Gets the current scaffolder for the command.
     *
     * @return Scaffolder
     */
    protected function scaffolder(): Scaffolder
    {
        if (!isset($this->scaffolder)) {
            $this->scaffolder = new Scaffolder($this->sourceRoot(), base_path());
        }

        return $this->scaffolder;
    }

    /**
     * Creates a new file from the template content given.
     *
     * @param string                $path           Destination for the file to create.
     * @param string                $content        Template to place into the new file.
     * @param array<string,mixed>   $data           Optional data to pass to the template.
     *
     * @return void
     */
    protected function create(string $path, string $content, array $data = []): void
    {
        $this->ensureDirectoryExists(dirname($path));

        $this->scaffolder()->stubTemplate($content, $path, $data);

        $this->printScaffoldAction('create', $path);
    }

    /**
     * Copies an existing file to the given destination path.
     *
     * @param string                $path           Path of the file to copy.
     * @param string                $destination    Destination for the file to copy to.
     * @param array<string,mixed>   $data           Optional data to pass to the template.
     *
     * @return void
     */
    protected function copy(string $path, string $destination, array $data = []): void
    {
        $this->ensureDirectoryExists(dirname($destination));

        $this->scaffolder()->stub($path, $destination, $data);

        $this->printScaffoldAction('create', $destination);
    }

    /**
     * Ensures that the directory given exists in the output directory.
     *
     * @param   string  $path       Path to the directory, relative to the destination root.
     *
     * @return void
     */
    private function ensureDirectoryExists(string $path): void
    {
        if (@is_dir($path)) {
            return;
        }

        @mkdir($path, recursive: true);

        $this->printScaffoldAction('exist', $path);
    }

    /**
     * Prints a log message for the given scaffolding action.
     *
     * @param   string  $action         Scaffolding action performed.
     * @param   string  $arg            Argument to show next to the action.
     *
     * @return void
     */
    private function printScaffoldAction(string $action, string $arg): void
    {
        $actionColor = match ($action) {
            'create' => 'text-green-400',
            'exist' => 'text-indigo-400',
            'update' => 'text-teal-500',
            'delete' => 'text-red-500',
            default => 'text-gray-500',
        };

        $this->writeHtml(<<<HTML
            <div class='text-gray-400'>
                <div class="w-12 {$actionColor} font-bold text-right mr-2">{$action}</div>
                {$arg}
            </div>
        HTML);
    }
}
