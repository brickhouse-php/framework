<?php

namespace Brickhouse\Console\Prompts;

enum PromptState
{
    case INITIAL;
    case ACTIVE;
    case CANCEL;
    case SUBMIT;
    case ERROR;
}
