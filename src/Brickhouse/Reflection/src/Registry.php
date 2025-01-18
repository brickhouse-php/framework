<?php

namespace Brickhouse\Reflection;

use Brickhouse\Core\Composer;
use Brickhouse\Support\Collection;

final class Registry
{
    /**
     * Contains all found classes in the registry.
     *
     * @var Collection<string,string>
     */
    private Collection $classes;

    public function __construct(private readonly Composer $composer)
    {
        $this->classes = Collection::empty();
    }

    /**
     * Get all the found classes in the registry.
     *
     * @return Collection<string,string>
     */
    public function classes(): Collection
    {
        return $this->classes;
    }

    /**
     * Get all the found classes in the registry.
     *
     * @return Collection<int,ReflectedType<object>>
     */
    public function reflectors(): Collection
    {
        /** @var Collection<int,ReflectedType<object>> $reflectors */
        $reflectors = $this->classes()->map(fn(string $className) => new ReflectedType($className));

        return $reflectors;
    }

    /**
     * Gets a builder for getting classes which meet the requirements.
     *
     * @return ReflectorListBuilder
     */
    public function query(): ReflectorListBuilder
    {
        return new ReflectorListBuilder($this->reflectors());
    }

    /**
     * Index all the files and directories in the given base path.
     *
     * @param string|array<string>  $paths  Where to look for files and classes.
     *
     * @return void
     */
    public function index(string|array $paths): void
    {
        if (is_string($paths)) {
            $paths = [$paths];
        }

        $this->composer->loader->getClassMap();

        foreach ($this->composer->loader->getClassMap() as $class => $_) {
            $this->classes->push($class);
        }
    }
}
