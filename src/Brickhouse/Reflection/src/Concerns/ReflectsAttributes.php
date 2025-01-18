<?php

namespace Brickhouse\Reflection\Concerns;

use Brickhouse\Reflection\ReflectedAttribute;

trait ReflectsAttributes
{
    /**
     * Get all the attributes on the instance, using PHP's Reflection API.
     *
     * @return array<int,\ReflectionAttribute<object>>
     */
    abstract protected function getAttributes(): array;

    /**
     * Gets all the attributes on the method.
     *
     * @param null|list<class-string>|string    $names      Optional class name to filter attributes from.
     * @param bool                              $inherit    If `$name` is non-null, defines whether derived attributes should be included.
     *
     * @return array<ReflectedAttribute<object>>
     */
    public function attributes(null|string|array $names = null, bool $inherit = true): array
    {
        $attributes = array_map(
            fn(\ReflectionAttribute $attribute) => new ReflectedAttribute($attribute),
            $this->getAttributes()
        );

        if ($names !== null) {
            $names = array_wrap($names);

            $attributes = array_filter($attributes, function (ReflectedAttribute $attribute) use (
                $names,
                $inherit
            ): bool {
                $attributeName = $attribute->name;

                if (in_array($attributeName, $names)) {
                    return true;
                }

                // If `$inherit` is `true`, we'll allow attributes which are derived from
                // the requested attribute.
                if ($inherit && array_any($names, fn(string $name) => is_subclass_of($attributeName, $name))) {
                    return true;
                }

                return false;
            });
        }

        return $attributes;
    }

    /**
     * Gets the first attribute on the method with the given type.
     *
     * @template TAttribute of object
     *
     * @param class-string<TAttribute>  $name       Class name to filter attributes from.
     * @param bool                      $inherit    If `$name` is non-null, defines whether derived attributes should be included.
     *
     * @return null|ReflectedAttribute<TAttribute>
     */
    public function attribute(string $name, bool $inherit = true): null|ReflectedAttribute
    {
        $attributes = $this->attributes($name, $inherit);

        if ($current = current($attributes)) {
            /** @phpstan-ignore return.type */
            return $current;
        }

        return null;
    }
}
