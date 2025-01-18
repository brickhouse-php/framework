<?php

namespace Brickhouse\View;

use Brickhouse\View\Engine\Compiler;

class Renderer extends \Brickhouse\View\Engine\Renderer
{
    public function __construct()
    {
        parent::__construct(
            new ViewResolver(),
            new Compiler(),
        );
    }
}
