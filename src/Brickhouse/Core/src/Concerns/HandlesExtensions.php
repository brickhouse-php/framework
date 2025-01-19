<?php

namespace Brickhouse\Core\Concerns;

use Brickhouse\Core\Extension;
use Brickhouse\Support\Collection;

trait HandlesExtensions
{
    /**
     * Gets all the available extensions in the application.
     *
     * @var array<string,Extension>
     */
    protected array $extensions = [];

    /**
     * Gets all the available extensions, sorted after their load order.
     *
     * @var array<int,class-string<Extension>>
     */
    private array $loadOrder = [];

    /**
     * Gets all bootstrapping extensions. These are needed for most other extensions to function.
     *
     * @var array<int,class-string<Extension>>
     */
    private array $bootstrapping = [
        \Brickhouse\Config\Extension::class,
        \Brickhouse\Log\Extension::class,
    ];

    /**
     * Gets all the extensions which have finished registration.
     *
     * @var array<class-string<Extension>,bool>
     */
    private array $registered = [];

    /**
     * Gets all the extensions which have finished booting.
     *
     * @var array<class-string<Extension>,bool>
     */
    private array $booted = [];

    /**
     * Gets all the extensions of the application.
     *
     * @return Collection<string,Extension>
     */
    public function extensions(): Collection
    {
        return Collection::wrap($this->extensions);
    }

    /**
     * Initialize all the found extensions in the application.
     *
     * @return void
     */
    protected function initializeExtensions(): void
    {
        // Register the most important extensions first, as most others would fail without them.
        $this->registerExtensions($this->bootstrapping);

        // Load the rest of the extensions and sort them after their load order.
        $loadOrder = $this->findExtensions();

        // Register the remaining extensions.
        $this->registerExtensions($loadOrder);
    }

    /**
     * Register all the found extensions in the application.
     *
     * @param array<int,class-string<Extension>>    $extensions
     *
     * @return void
     */
    protected function registerExtensions(array $extensions): void
    {
        foreach ($extensions as $extension) {
            if (!class_exists($extension, autoload: false)) {
                continue;
            }

            if (in_array($extension, $this->registered)) {
                continue;
            }

            $instance = resolve($extension);
            $instance->register();

            $this->addExtension($instance);

            $this->registered[] = $extension;
        }
    }

    /**
     * Boot all the registered extensions in the application.
     *
     * @return void
     */
    protected function bootExtensions(): void
    {
        foreach ($this->extensions() as $extension) {
            if (in_array($extension::class, $this->booted)) {
                continue;
            }

            $extension->boot();

            $this->booted[] = $extension::class;
        }
    }

    /**
     * Adds the given extension to the application.
     *
     * @param Extension     $extension
     */
    public function addExtension(Extension $extension): void
    {
        $this->extensions[$extension->name] = $extension;
    }

    /**
     * Finds all the extensions defined in vendor packages.
     *
     * @return array<int,class-string<Extension>>
     */
    protected function findExtensions(): array
    {
        $packages = [
            ...glob(path(app()->vendorPath, '*', '*', 'composer.json')),
            ...glob(path(app()->basePath, 'composer.json')),
        ];

        /** @var array<int,class-string<Extension>> $extensions */
        $extensions = [];

        foreach ($packages as $composer) {
            $content = json_decode(
                file_get_contents($composer),
                associative: true
            );

            if (!isset($content['extra']['brickhouse']['extensions'])) {
                continue;
            }

            /** @var array<int,class-string<Extension>> */
            $packageExtensions = array_wrap($content['extra']['brickhouse']['extensions']);

            $extensions = [
                ...$extensions,
                ...$packageExtensions
            ];
        }

        // Remove all duplicates which might arise from different class-strings resolving
        // to the same class (e.g. `Namespace\Class` differs from `\Namespace\Class` but resolves to the same class.)
        $extensions = array_map(fn(string $extension) => trim($extension, '/\\'), $extensions);
        $extensions = array_unique($extensions);

        // Sort all the extensions for their load order.
        $extensions = $this->buildDependencyGraph($extensions);

        return $extensions;
    }

    /**
     * Builds a load order of all the given extensions.
     *
     * @param array<int,class-string<Extension>>    $extensions
     *
     * @return array<int,class-string<Extension>>
     */
    public function buildDependencyGraph(array $extensions): array
    {
        $loadOrder = [];

        // Map all extensions using their class-names as keys and dependencies as values.
        $extensions = array_reduce(
            $extensions,
            function (array $carry, string $extension) {
                $carry[$extension] = resolve($extension)->dependencies();
                return $carry;
            },
            []
        );

        while (!empty($extensions)) {
            // Get all extensions without any unloaded extensions.
            $dependencyLess = array_filter($extensions, fn(array $deps) => empty($deps));

            // Add the the dependency-less extensions to the load order and remove from the list.
            foreach (array_keys($dependencyLess) as $dep) {
                $loadOrder[] = $dep;
                unset($extensions[$dep]);
            }

            // Loop over all remaining extensions and remove all dependencies
            // that have been added to the load order.
            foreach ($extensions as $extension => $dependencies) {
                $extensions[$extension] = array_filter(
                    $dependencies,
                    fn(string $dependency) => !in_array($dependency, $loadOrder)
                );
            }
        }

        return $loadOrder;
    }
}
