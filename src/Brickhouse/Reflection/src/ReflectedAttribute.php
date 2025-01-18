<?php

namespace Brickhouse\Reflection;

/**
 * @template TAttribute of object
 */
final class ReflectedAttribute
{
    /**
     * Gets the underlying `ReflectionAttribute` instance.
     *
     * @var \ReflectionAttribute<TAttribute>
     */
    private readonly \ReflectionAttribute $instance;

    /**
     * Gets the name of the attribute.
     *
     * @var string
     */
    public readonly string $name;

    /**
     * Creates a new instance of the reflector with the given attribute.
     *
     * @param \ReflectionAttribute<TAttribute>  $attribute
     */
    public function __construct(\ReflectionAttribute $attribute)
    {
        $this->instance = $attribute;
        $this->name = $this->instance->getName();
    }

    /**
     * Gets the arguments for the attribute.
     *
     * @return array<string,mixed>
     */
    public function arguments(): array
    {
        return $this->instance->getArguments();
    }

    /**
     * Gets the target of the attribute.
     *
     * @return int
     */
    public function target(): int
    {
        return $this->instance->getTarget();
    }

    /**
     * Creates a new instance of the attribute.
     *
     * @return TAttribute
     */
    public function create()
    {
        return $this->instance->newInstance();
    }
}
