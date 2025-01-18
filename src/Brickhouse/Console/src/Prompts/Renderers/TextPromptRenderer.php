<?php

namespace Brickhouse\Console\Prompts\Renderers;

use Brickhouse\Console\Prompts\Console;
use Brickhouse\Console\Prompts\PromptState;

class TextPromptRenderer
{
    use RendersInput;

    public function __construct(
        protected readonly Console $console
    ) {}

    public function render(
        string $label,
        string $value,
        PromptState $state,
        string $placeholder = '',
        string $hint = '',
        null|string $error = null,
    ): string {
        $this->prepare($state);

        $this->label($label);

        if ($error !== null) {
            $this->write("<span class='{$this->theme()}'>— {$error}</span>");
        } else if (mb_strlen($hint) > 0) {
            $this->write("<span class='text-black'>— {$hint}</span>");
        }

        $this->newline();

        if ($state === PromptState::SUBMIT) {
            $this->line("<span class='text-black'>{$value}</span>");
            return $this->finalize();
        };

        if (empty($value) && !empty($placeholder)) {
            $this->line("<span class='text-black'>{$placeholder}</span>");
        } else {
            $this->line("<span>{$value}</span>");
        }

        return $this->finalize();
    }

    protected function console(): Console
    {
        return $this->console;
    }
}
