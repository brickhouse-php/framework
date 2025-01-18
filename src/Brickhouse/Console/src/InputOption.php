<?php

namespace Brickhouse\Console;

enum InputOption
{
    case NONE;
    case OPTIONAL;
    case REQUIRED;
    case NEGATABLE;
}
