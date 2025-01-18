<?php

namespace Brickhouse\Console\Attributes;

use Attribute;
use Brickhouse\Console\InputOption;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Argument
{
    public function __construct(
        public readonly string $name,
        public readonly null|string $description = null,
        public readonly InputOption $input = InputOption::NONE,
        public readonly null|int $order = null,
    ) {}
}
