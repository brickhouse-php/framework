<?php

namespace Brickhouse\Reflection;

/**
 * @template T of object
 */
final class ReflectedType
{
    use \Brickhouse\Reflection\Concerns\ReflectsAttributes;

    /**
     * Gets the underlying `ReflectionClass` instance.
     *
     * @var \ReflectionClass<T>
     */
    private readonly \ReflectionClass $instance;

    /**
     * Gets the name of the type.
     *
     * @var class-string<T>
     */
    public readonly string $name;

    /**
     * Creates a new instance of the reflector with the given type.
     *
     * @param class-string<T>   $type
     *
     * @throws \ReflectionException
     */
    public function __construct(string $type)
    {
        $this->instance = new \ReflectionClass($type);
        $this->name = $this->instance->name;
    }

    /**
     * Gets whether the type is instantiable.
     *
     * @return boolean
     */
    public function instantiable(): bool
    {
        return $this->instance->isInstantiable();
    }

    /**
     * Creates a new instance of the type.
     *
     * @return T
     */
    public function newInstance(mixed ...$args)
    {
        return $this->instance->newInstance(...$args);
    }

    /**
     * Creates a new instance of the type, without invoking the constructor.
     *
     * @return T
     */
    public function newInstanceWithoutConstructor()
    {
        return $this->instance->newInstanceWithoutConstructor();
    }

    /**
     * Gets whether the type is an interface.
     *
     * @return boolean
     */
    public function interface(): bool
    {
        return $this->instance->isInterface();
    }

    /**
     * Gets whether the type is a trait.
     *
     * @return boolean
     */
    public function trait(): bool
    {
        return $this->instance->isTrait();
    }

    /**
     * Gets whether the type is abstract.
     *
     * @return boolean
     */
    public function abstract(): bool
    {
        return $this->instance->isAbstract();
    }

    /**
     * Gets whether the type is anonymous.
     *
     * @return boolean
     */
    public function anonymous(): bool
    {
        return $this->instance->isAnonymous();
    }

    /**
     * Gets whether the type is an enum.
     *
     * @return boolean
     */
    public function enum(): bool
    {
        return $this->instance->isEnum();
    }

    /**
     * Gets whether the type is a class.
     *
     * @return boolean
     */
    public function class(): bool
    {
        return !$this->interface() && !$this->enum() && !$this->trait();
    }

    /**
     * Gets the path of the file or whether it matches the given pattern, if given.
     *
     * @param ?string   $pattern    Optional glob-style pattern to match the file path against.
     *
     * @return ($pattern is string ? boolean : string)
     */
    public function path(?string $pattern = null)
    {
        $path = $this->instance->getFileName();

        if (!$pattern) {
            return $path;
        }

        return fnmatch($pattern, $path);
    }

    /**
     * Gets whether the type extends from the given type.
     *
     * @param class-string  $class
     *
     * @return boolean
     */
    public function extends(string $class): bool
    {
        return $this->instance->isSubclassOf($class);
    }

    /**
     * Gets whether the type implements the given interface type.
     *
     * @param class-string  $interface
     *
     * @return boolean
     */
    public function implements(string $interface): bool
    {
        return $this->instance->implementsInterface($interface);
    }

    /**
     * Gets whether the type uses the given trait type.
     *
     * @param class-string  $trait      The trait type name to check for.
     * @param bool          $recursive  Whether to check inherited classes for traits.
     *
     * @return boolean
     */
    public function uses(string $trait, bool $recursive = true): bool
    {
        $traits = $this->instance->getTraitNames();

        $recursiveClasses = function (\ReflectionClass $class) use (&$recursiveClasses, &$traits) {
            if ($parent = $class->getParentClass()) {
                $recursiveClasses($parent);
            } else {
                $traits = array_merge($traits, $class->getTraitNames());
            }
        };

        if ($recursive) {
            $recursiveClasses($this->instance);
        }

        return in_array($trait, $traits);
    }

    /**
     * Gets all the public methods on the type.
     *
     * @return array<ReflectedMethod>
     */
    public function getPublicMethods(): array
    {
        return array_map(
            fn(\ReflectionMethod $method) => new ReflectedMethod($method),
            $this->instance->getMethods(\ReflectionMethod::IS_PUBLIC)
        );
    }

    /**
     * Gets the value of the given static property, if it exists. Otherwise, returns `$default`.
     *
     * @return mixed
     */
    public function getStaticPropertyValue(string $property, mixed $default = null): mixed
    {
        return $this->instance->getStaticPropertyValue($property, $default);
    }

    /**
     * Gets the property with the given name.
     *
     * @return null|ReflectedProperty
     */
    public function getProperty(string $name): null|ReflectedProperty
    {
        try {
            $property = $this->instance->getProperty($name);
            return new ReflectedProperty($property);
        } catch (\ReflectionException) {
            return null;
        }
    }

    /**
     * Gets all the properties on the type.
     *
     * @return array<int,ReflectedProperty>
     */
    public function getProperties(): array
    {
        return array_map(
            fn(\ReflectionProperty $property) => new ReflectedProperty($property),
            $this->instance->getProperties()
        );
    }

    /**
     * @inheritDoc
     *
     * @return list<\ReflectionAttribute<object>>
     */
    protected function getAttributes(): array
    {
        return $this->instance->getAttributes();
    }
}
