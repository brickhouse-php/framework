<?php

namespace Brickhouse\Support;

interface Renderable
{
    /**
     * Get the rendered content of the model.
     *
     * @return string
     */
    public function render(): string;
}
