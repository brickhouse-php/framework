<?php

namespace Brickhouse\Core;

use Composer\Autoload\ClassLoader;

final class Composer
{
    /**
     * Gets the absolute path to the `composer.json` file.
     *
     * @var string
     */
    public readonly string $path;

    /**
     * Gets the `composer.json` file as an array.
     *
     * @var array<string,mixed>
     */
    public readonly array $composer;

    /**
     * Gets the `ClassLoader` instance resposible for loading Brickhouse packages.
     *
     * @var ClassLoader
     */
    public readonly ClassLoader $loader;

    public function __construct(private readonly string $root)
    {
        $this->path = $this->root . "/composer.json";
        if (!file_exists($this->path)) {
            throw new \Exception("Failed to find 'composer.json'");
        }

        $this->composer = json_decode(file_get_contents($this->path), associative: true);
        $this->loader = $this->getClassLoader($root);
    }

    private function getClassLoader(string $root): ClassLoader
    {
        $loaders = ClassLoader::getRegisteredLoaders();
        $prefix = $root . "/vendor";

        if (!isset($loaders[$prefix])) {
            throw new \Exception("Failed to find appropriate class loader");
        }

        return $loaders[$prefix];
    }
}
