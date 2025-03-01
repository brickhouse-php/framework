<?php

namespace Brickhouse\Reflection;

if (class_exists('\Brickhouse\Support\Collection')) {
    final class ReflectorListBuilder
    {
        /**
         * @param \Brickhouse\Support\Collection<int,ReflectedType<object>> $reflectors Gets all the reflectors from the builder.
         */
        public function __construct(private \Brickhouse\Support\Collection $reflectors) {}

        /**
         * Get all the types which are classes.
         *
         * @return static
         */
        public function classes(): static
        {
            $this->reflectors = $this->reflectors->filter(fn(ReflectedType $reflector) => $reflector->class());

            return $this;
        }

        /**
         * Get all the types which implement the given interface.
         *
         * @param string|list<string>   $interfaces
         *
         * @return static
         */
        public function implements(string|array $interfaces): static
        {
            if (is_string($interfaces)) {
                $interfaces = [$interfaces];
            }

            $this->reflectors = $this->reflectors->filter(
                fn(ReflectedType $reflector) => array_any(
                    $interfaces,
                    fn(string $interface) => $reflector->implements($interface)
                )
            );

            return $this;
        }

        /**
         * Gets all the filtered classes left in the builder.
         *
         * @return \Brickhouse\Support\Collection<int,ReflectedType<object>>
         */
        public function get(): \Brickhouse\Support\Collection
        {
            return $this->reflectors;
        }
    }
}
