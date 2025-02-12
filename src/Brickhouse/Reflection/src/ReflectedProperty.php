<?php

namespace Brickhouse\Reflection;

final class ReflectedProperty
{
    use \Brickhouse\Reflection\Concerns\ReflectsAttributes;

    /**
     * Gets the underlying `ReflectionProperty` instance.
     *
     * @var \ReflectionProperty
     */
    private readonly \ReflectionProperty $instance;

    /**
     * Gets the name of the method.
     *
     * @var string
     */
    public string $name { get => $this->instance->name; }

    /**
     * Gets the default value of the property, if it is set. Otherwise, `null`.
     *
     * @var mixed
     */
    public mixed $default {
        get {
            if ($this->instance->hasDefaultValue()) {
                return $this->instance->getDefaultValue();
            }

            return null;
        }
    }

    /**
     * Creates a new instance of the reflector with the given method.
     *
     * @param \ReflectionProperty     $property
     */
    public function __construct(\ReflectionProperty $property)
    {
        $this->instance = $property;
    }

    /**
     * Gets the type of the property, if any is set.
     *
     * @return null|\ReflectionType
     */
    public function type(): null|\ReflectionType
    {
        return $this->instance->getType();
    }

    /**
     * Gets whether the property is abstract.
     *
     * @return boolean
     */
    public function abstract(): bool
    {
        return $this->instance->isAbstract();
    }

    /**
     * Gets whether the property is public.
     *
     * @return boolean
     */
    public function public(): bool
    {
        return $this->instance->isPublic();
    }

    /**
     * Gets whether the property is protected.
     *
     * @return boolean
     */
    public function protected(): bool
    {
        return $this->instance->isProtected();
    }

    /**
     * Gets whether the property is private.
     *
     * @return boolean
     */
    public function private(): bool
    {
        return $this->instance->isPrivate();
    }

    /**
     * Gets whether the property is static.
     *
     * @return boolean
     */
    public function static(): bool
    {
        return $this->instance->isStatic();
    }

    /**
     * Gets whether the property is read-only.
     *
     * @return boolean
     */
    public function readonly(): bool
    {
        return $this->instance->isReadOnly();
    }

    /**
     * Gets whether the property is virtual.
     *
     * @return boolean
     */
    public function virtual(): bool
    {
        return $this->instance->isVirtual();
    }

    /**
     * Gets whether the property is hooked.
     *
     * @return boolean
     */
    public function hooked(): bool
    {
        return $this->instance->hasHooks();
    }

    /**
     * Gets the property value, given some instance.
     *
     * @param null|object   $instance
     *
     * @return mixed
     */
    public function value(null|object $instance = null): mixed
    {
        return $this->instance->getValue($instance);
    }

    /**
     * Sets the property value, given some instance.
     *
     * @param null|object   $instance
     * @param mixed         $value
     *
     * @return void
     */
    public function setValue(null|object $instance, mixed $value): void
    {
        $this->instance->setValue($instance, $value);
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
