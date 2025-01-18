<?php

namespace Brickhouse\Console\Prompts\Contracts;

use Brickhouse\Console\Prompts\CursorPosition;

interface HasCursor
{
    public function getCursorPosition(): CursorPosition;
}
