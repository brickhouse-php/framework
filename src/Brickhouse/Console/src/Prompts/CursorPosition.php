<?php

namespace Brickhouse\Console\Prompts;

final readonly class CursorPosition
{
    public function __construct(
        public readonly int $x,
        public readonly int $y,
    ) {}
}
