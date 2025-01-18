<?php

namespace Brickhouse\Console\Prompts\Renderers;

use Brickhouse\Console\Prompts\Console;
use Brickhouse\Console\Prompts\PromptState;

class PausePromptRenderer
{
    use RendersInput;

    public function __construct(
        protected readonly Console $console
    ) {}

    public function render(
        string $label,
        PromptState $state,
    ): string {
        $this->prepare($state);

        $this->label($label);
        $this->newline();

        if ($state === PromptState::SUBMIT) {
            return $this->finalize();
        };

        $this->line("<span>{$label}</span>");

        return $this->finalize();
    }

    protected function console(): Console
    {
        return $this->console;
    }
}
