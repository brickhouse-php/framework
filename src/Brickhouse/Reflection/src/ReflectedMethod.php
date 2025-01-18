<?php

namespace Brickhouse\Reflection;

final class ReflectedMethod
{
    use \Brickhouse\Reflection\Concerns\ReflectsAttributes;

    /**
     * Gets the underlying `ReflectionMethod` instance.
     *
     * @var \ReflectionMethod
     */
    private readonly \ReflectionMethod $instance;

    /**
     * Gets the name of the method.
     *
     * @var string
     */
    public readonly string $name;

    /**
     * Creates a new instance of the reflector with the given method.
     *
     * @param \ReflectionMethod     $method
     */
    public function __construct(\ReflectionMethod $method)
    {
        $this->instance = $method;
        $this->name = $this->instance->name;
    }

    /**
     * Gets whether the method is a constructor.
     *
     * @return boolean
     */
    public function constructor(): bool
    {
        return $this->instance->isConstructor();
    }

    /**
     * Gets whether the method is a destructor.
     *
     * @return boolean
     */
    public function destructor(): bool
    {
        return $this->instance->isDestructor();
    }

    /**
     * Gets whether the method is abstract.
     *
     * @return boolean
     */
    public function abstract(): bool
    {
        return $this->instance->isAbstract();
    }

    /**
     * Gets whether the method is static.
     *
     * @return boolean
     */
    public function static(): bool
    {
        return $this->instance->isStatic();
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
