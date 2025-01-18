<?php

namespace Brickhouse\Console\Attributes;

use Attribute;
use Brickhouse\Console\InputOption;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Option
{
    public function __construct(
        public readonly string $name,
        public readonly null|string $shortName = null,
        public readonly null|string $description = null,
        public readonly InputOption $input = InputOption::NONE,
    ) {}
}
