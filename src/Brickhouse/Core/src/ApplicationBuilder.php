<?php

namespace Brickhouse\Core;

class ApplicationBuilder
{
    public function __construct(
        private readonly string $basePath
    ) {}

    public function create(): Application
    {
        return new Application($this->basePath);
    }
}
