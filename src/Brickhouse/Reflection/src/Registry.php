<?php

namespace Brickhouse\Reflection;

if (class_exists('\Brickhouse\Core\Composer') && class_exists('\Brickhouse\Support\Collection')) {
    final class Registry
    {
        /**
         * Contains all found classes in the registry.
         *
         * @var \Brickhouse\Support\Collection<string,string>
         */
        private \Brickhouse\Support\Collection $classes;

        public function __construct(
            private readonly \Brickhouse\Core\Composer $composer
        ) {
            $this->classes = \Brickhouse\Support\Collection::empty();
        }

        /**
         * Get all the found classes in the registry.
         *
         * @return \Brickhouse\Support\Collection<string,string>
         */
        public function classes(): \Brickhouse\Support\Collection
        {
            return $this->classes;
        }

        /**
         * Get all the found classes in the registry.
         *
         * @return \Brickhouse\Support\Collection<int,ReflectedType<object>>
         */
        public function reflectors(): \Brickhouse\Support\Collection
        {
            /** @var \Brickhouse\Support\Collection<int,ReflectedType<object>> $reflectors */
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
}
