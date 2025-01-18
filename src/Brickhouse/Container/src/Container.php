<?php

namespace Brickhouse\Container;

use Brickhouse\Container\Exceptions\ContainerEntryMissingException;
use Brickhouse\Container\Exceptions\ResolutionFailedException;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    /**
     * Gets the globally available instance of the container, if any.
     */
    protected static Container $container;

    /**
     * Defines all the containers bindings.
     *
     * @var array<string,array{concrete:string,shared:bool}>
     */
    protected array $bindings = [];

    /**
     * Defines all the containers type alises.
     *
     * @var array<string,string>
     */
    protected array $aliases = [];

    /**
     * Defines all the containers singleton instances.
     *
     * @var array<string,mixed>
     */
    protected array $instances = [];

    /**
     * Gets whether the container has any entries defined with the given type.
     *
     * @param string $abstract
     *
     * @return boolean
     */
    public function has(string $abstract): bool
    {
        return isset($this->bindings[$abstract]) || $this->isAlias($abstract);
    }

    /**
     * Gets the instance from the container with the given abstract name.
     *
     * This method exists to be compatible with PSR-11.
     *
     * @param string $abstract
     *
     * @return mixed
     *
     * @throws ContainerEntryMissingException       Thrown if the entry isn't found in the container.
     */
    public function get(string $abstract): mixed
    {
        if (($binding = $this->getBinding($abstract)) === null) {
            throw new ContainerEntryMissingException($abstract);
        }

        if (is_callable($binding)) {
            return $this->call(null, $binding);
        }

        return $this->resolve($binding);
    }

    /**
     * Aliases the given abstract type to the given concrete type.
     *
     * @param string $abstract
     * @param string $concrete
     *
     * @return void
     */
    public function alias(string $abstract, string $concrete): void
    {
        if ($abstract === $concrete) {
            throw new \Exception("Cannot alias abstract to same type: {$abstract}");
        }

        $this->aliases[$abstract] = $concrete;
    }

    /**
     * Gets the alias of the given type, if any. Otherwise, returns the given type.
     *
     * @param string $abstract
     *
     * @return string
     */
    public function getAlias(string $abstract): string
    {
        if (!$this->isAlias($abstract)) {
            return $abstract;
        }

        return $this->aliases[$abstract];
    }

    /**
     * Gets whether the given type is registered as an alias for another bound type.
     *
     * @param string $abstract
     *
     * @return boolean
     */
    public function isAlias(string $abstract): bool
    {
        return isset($this->aliases[$abstract]);
    }

    /**
     * Gets the bound concrete type, which is bound to the given type.
     *
     * @param string $abstract
     *
     * @return \Closure|string|null
     */
    public function getBinding(string $abstract): \Closure|string|null
    {
        // If the abstract is an alias, resolve it first.
        $abstract = $this->getAlias($abstract);

        if (isset($this->bindings[$abstract])) {
            return $this->bindings[$abstract]["concrete"];
        }

        return null;
    }

    /**
     * Gets whether the given type is bound as shared. If not bound, returns `false`.
     *
     * @param string $abstract
     *
     * @return bool
     */
    public function isShared(string $abstract): bool
    {
        // If the abstract is an alias, resolve it first.
        $abstract = $this->getAlias($abstract);

        if (isset($this->bindings[$abstract])) {
            return $this->bindings[$abstract]["shared"];
        }

        return false;
    }

    /**
     * Binds a value to the given type in the container.
     *
     * @param string                $abstract
     * @param \Closure|string|null  $concrete
     * @param bool                  $shared
     *
     * @return void
     */
    public function bind(string $abstract, \Closure|string|null $concrete = null, bool $shared = false): void
    {
        $concrete ??= $abstract;

        $this->bindings[$abstract] = [
            "concrete" => $concrete,
            "shared" => $shared,
        ];
    }

    /**
     * Binds a value to the given type in the container, if the type has not already been bound.
     *
     * @param string                $abstract
     * @param \Closure|string|null  $concrete
     * @param bool                      $shared
     *
     * @return void
     */
    public function bindIf(string $abstract, \Closure|string|null $concrete = null, bool $shared = false): void
    {
        if (!$this->has($abstract)) {
            $this->bind($abstract, $concrete, $shared);
        }
    }

    /**
     * Binds a value to the given type in the container as a singleton type.
     *
     * @param string                $abstract
     * @param \Closure|string|null  $concrete
     *
     * @return void
     */
    public function singleton(string $abstract, \Closure|string|null $concrete = null): void
    {
        $this->bind($abstract, $concrete, shared: true);
    }

    /**
     * Binds a value to the given type in the container as a singleton type, if the type has not already been bound.
     *
     * @param string                $abstract
     * @param \Closure|string|null  $concrete
     *
     * @return void
     */
    public function singletonIf(string $abstract, \Closure|string|null $concrete = null): void
    {
        if (!$this->has($abstract)) {
            $this->singleton($abstract, $concrete);
        }
    }

    /**
     * Binds a value to the given type in the container as a singleton type.
     *
     * @param string   $abstract
     * @param mixed    $instance
     *
     * @return void
     */
    public function instance(string $abstract, $instance): void
    {
        $this->singleton($abstract, fn() => $instance);
    }

    /**
     * Binds a value to the given type in the container as a singleton type, if the type has not already been bound.
     *
     * @param string   $abstract
     * @param mixed    $instance
     *
     * @return void
     */
    public function instanceIf(string $abstract, $instance): void
    {
        if (!$this->has($abstract)) {
            $this->instance($abstract, $instance);
        }
    }

    /**
     * Resolves the given type from the container.
     *
     * @template T
     *
     * @param string|class-string<T>    $abstract
     * @param array<array-key,mixed>    $parameters
     * @param array<int,string>         $buildStack
     *
     * @return ($abstract is class-string<T> ? T : mixed)
     */
    public function resolve(string $abstract, array $parameters = [], array $buildStack = [])
    {
        $concrete = $this->getBinding($abstract);
        $needsContextualBuild = !empty($parameters);

        // If the type allows for shared instances and one is already set, return the
        // existing one. If the parameters differ from the default, create a new instance.
        if (isset($this->instances[$abstract]) && !$needsContextualBuild) {
            return $this->instances[$abstract];
        }

        $concrete ??= $abstract;

        // Recursively build all dependencies until resolved, so we can build the
        // requested type.
        $instance = $this->build($concrete, $parameters, $buildStack);

        // If the type allows to be shared, save it for future resolutions.
        if ($this->isShared($abstract) && !$needsContextualBuild) {
            $this->instances[$abstract] = $instance;
        }

        return $instance;
    }

    /**
     * Resolves the parameters from the given callable and invokes it.
     *
     * @template TThis
     * @template TReturn
     *
     * @param null|TThis                $newThis    Optional object to bind the callable to.
     * @param callable():TReturn        $callable
     * @param array<array-key,mixed>    $parameters
     * @param array<int,string>         $buildStack
     *
     * @return TReturn
     */
    public function call($newThis, callable $callable, array $parameters = [], array $buildStack = [])
    {
        $buildStack[] = $callable;

        try {
            $reflector = new \ReflectionFunction($callable);
        } catch (\ReflectionException $e) {
            throw new ResolutionFailedException("Target closure does not exist.", $buildStack, $e);
        }

        $dependencies = $reflector->getParameters();
        $instances = $this->resolveDependencies($dependencies, $parameters, $buildStack);

        if ($callable instanceof \Closure && $newThis !== null) {
            $result = $callable->call($newThis, ...$instances);
        } else {
            $result = $callable(...$instances);
        }

        return $result;
    }

    /**
     * Build an instance of the given concrete type.
     *
     * @param \Closure|string           $concrete
     * @param array<array-key,mixed>    $parameters
     * @param array<int,string>         $buildStack
     *
     * @return mixed
     */
    public function build(\Closure|string $concrete, array $parameters = [], array $buildStack = [])
    {
        $buildStack[] = $concrete;

        try {
            $reflector = new \ReflectionClass($concrete);
        } catch (\ReflectionException $e) {
            throw new ResolutionFailedException("Target class [$concrete] does not exist.", $buildStack, $e);
        }

        if (!$concrete instanceof \Closure && !$reflector->isInstantiable()) {
            throw new ResolutionFailedException("Target type [$concrete] cannot be instantiated.", $buildStack);
        }

        $constructor = $reflector->getConstructor();

        if ($constructor === null) {
            $instance = new $concrete();

            return $instance;
        }

        $dependencies = $constructor->getParameters();
        $instances = $this->resolveDependencies($dependencies, $parameters, $buildStack);

        if ($concrete instanceof \Closure) {
            $instance = $concrete(...$instances);
        } else {
            $instance = $reflector->newInstanceArgs($instances);
        }

        return $instance;
    }

    /**
     * Resolve all the dependencies in the given parameter list.
     *
     * @param array<int,\ReflectionParameter>   $dependencies
     * @param array<string,mixed>               $parameters
     * @param array<int,string>                 $buildStack
     *
     * @return array<int,mixed>
     */
    protected function resolveDependencies(array $dependencies, array $parameters = [], array $buildStack = []): array
    {
        $results = [];

        foreach ($dependencies as $dependency) {
            $isPrimitive = $this->getParameterClass($dependency) === null;

            $name = $dependency->getName();
            $type = (string) $dependency->getType();

            // If a parameter of the same name is provided, use that instead.
            if (isset($parameters[$name])) {
                $results[] = $parameters[$name];
                continue;
            }

            // If no named parameter is provided, but one is provided of the type,
            // we can attempt to use that.
            $matchingParameter = array_find(
                $parameters,
                fn($value) => (is_object($value) ? $value::class : gettype($value)) === $type
            );

            if ($matchingParameter !== null) {
                $results[] = $matchingParameter;
                continue;
            }

            $results[] = $isPrimitive
                ? $this->resolvePrimitive($dependency, $buildStack)
                : $this->resolveClass($dependency, $buildStack);
        }

        return $results;
    }

    /**
     * Resolves the value of the given primitive type.
     *
     * @param \ReflectionParameter  $parameter
     * @param array<int,string>     $buildStack
     *
     * @return mixed
     */
    private function resolvePrimitive(\ReflectionParameter $parameter, array $buildStack = []): mixed
    {
        // Resolve a non-class hinted primitive dependency.
        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        if ($parameter->isVariadic()) {
            return [];
        }

        if ($parameter->hasType() && $parameter->allowsNull()) {
            return null;
        }

        throw new ResolutionFailedException(
            "Unresolvable dependency [$parameter] in class {$parameter->getDeclaringClass()->getName()}",
            $buildStack
        );
    }

    /**
     * Resolves the value of the given class type.
     *
     * @param \ReflectionParameter  $parameter
     * @param array<int,string>     $buildStack
     *
     * @return mixed
     */
    private function resolveClass(\ReflectionParameter $parameter, array $buildStack = []): mixed
    {
        try {
            return $this->resolve($this->getParameterClass($parameter), [], $buildStack);
        } catch (ResolutionFailedException $e) {
            if ($parameter->isDefaultValueAvailable()) {
                return $parameter->getDefaultValue();
            }

            throw $e;
        }
    }

    /**
     * Gets the class name of the given parameter.
     *
     * @param \ReflectionParameter $parameter
     *
     * @return ?string
     */
    private function getParameterClass(\ReflectionParameter $parameter): ?string
    {
        $type = $parameter->getType();

        if (!$type instanceof \ReflectionNamedType || $type->isBuiltin()) {
            return null;
        }

        $name = $type->getName();

        return $name;
    }
}
