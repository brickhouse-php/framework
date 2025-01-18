<?php

namespace Brickhouse\Console;

use Brickhouse\Console\Attributes\Argument;
use Brickhouse\Console\Attributes\Option;
use Brickhouse\Support\StringHelper;

use function Brickhouse\Console\Prompts\confirm;

abstract class GeneratorCommand extends Command
{
    public const string ROOT_NAMESPACE_PLACEHOLDER = 'RootNamespacePlaceholder';

    public const string NAMESPACE_PLACEHOLDER = 'NamespacePlaceholder';

    public const string CLASS_PLACEHOLDER = 'ClassNamePlaceholder';

    /**
     * The type of the class generated.
     *
     * @var string
     */
    public abstract string $type { get; }

    /**
     * Defines the name of the generated class.
     *
     * @var string
     */
    #[Argument("name", "Specifies the name of the class", InputOption::REQUIRED)]
    public string $className = '';

    /**
     * Defines which namespace to place the class into.
     *
     * @var string
     */
    #[Option("namespace", null, "Defines the namespace of the new class", InputOption::REQUIRED)]
    public null|string $namespace = null;

    /**
     * Defines whether to overwrite any existing files.
     *
     * @var bool
     */
    #[Option("force", "f", "Whether to override the existing file, if it exists", InputOption::NEGATABLE)]
    public bool $force = false;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->namespace ??= $this->defaultNamespace($this->rootNamespace());

        $stubPath = $this->stub();

        if (!file_exists($stubPath)) {
            $this->error("Stub file does not exist: {$stubPath}");
            return 1;
        }

        $this->className = StringHelper::from($this->className)
            ->trim()
            ->removeEnd('.php')
            ->__toString();

        $this->className = $this->getClass($this->className);

        $destination = $this->getPath($this->className);
        $content = $this->buildStub($stubPath, $this->className);

        $this->ensureDirectoryExists($destination);

        if (file_exists($destination)) {
            if (!$this->force && !confirm("File <span class='font-bold'>[{$destination}]</span> already exists. Overwrite?")) {
                return 0;
            }
        }

        file_put_contents($destination, $content);

        if (PHP_OS_FAMILY === 'Windows') {
            $destination = str_replace('/', '\\', $destination);
        }

        $this->info("{$this->type} <span class='font-bold'>[{$destination}]</span> created successfully.");

        return 0;
    }

    /**
     * Gets the stub file for the generator.
     *
     * @return string
     */
    protected abstract function stub(): string;

    /**
     * Gets the class name for the given class.
     *
     * @param  string  $className
     *
     * @return string
     */
    protected function getClass(string $className): string
    {
        return $className;
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
        // If the given name is already a path, return it as-is.
        if (str_starts_with($name, "./") || str_starts_with($name, ".\\")) {
            return $name;
        }

        $name = str_replace('\\', '/', $this->namespace . '/' . $name);
        $rootNamespace = str_replace('\\', '/', $this->rootNamespace());

        // If the name is prefixed with the root namespace, lower-case it.
        if (str_starts_with($name, $rootNamespace)) {
            $name = substr_replace($name, strtolower($rootNamespace), 0, strlen($rootNamespace));
        }

        return base_path() . '/' . $name . '.php';
    }

    /**
     * Gets the default namespace for the class.
     *
     * @param string    $rootNamespace
     *
     * @return string
     */
    protected function defaultNamespace(string $rootNamespace): string
    {
        return $rootNamespace;
    }

    /**
     * Gets the root namespace for the application.
     *
     * @return string
     */
    protected function rootNamespace(): string
    {
        return 'App\\';
    }

    /**
     * Builds the content of the stub.
     *
     * @return string
     */
    protected function buildStub(string $path, string $name): string
    {
        $content = file_get_contents($path);

        $content = str_replace(
            [self::ROOT_NAMESPACE_PLACEHOLDER, self::NAMESPACE_PLACEHOLDER, self::CLASS_PLACEHOLDER],
            [rtrim($this->rootNamespace(), '\\'), $this->namespace, $name],
            $content
        );

        return $content;
    }

    /**
     * Ensures that the directory of the given path exists.
     *
     * @return void
     */
    protected function ensureDirectoryExists(string $path): void
    {
        $directory = dirname($path);

        if (!is_dir($directory)) {
            mkdir($directory);
        }
    }
}
