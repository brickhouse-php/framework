<?php

namespace Brickhouse\Console\Prompts\Renderers;

use Brickhouse\Console\Prompts\Console;
use Brickhouse\Console\Prompts\PromptState;

class ConfirmPromptRenderer
{
    use RendersInput;

    public function __construct(
        protected readonly Console $console
    ) {}

    public function render(
        string $label,
        bool $value,
        PromptState $state,
        string $active,
        string $inactive,
        string $hint = '',
    ): string {
        $valueString = $value ? $active : $inactive;

        $this->prepare($state);
        $this->label($label);

        if (mb_strlen($hint) > 0) {
            $this->write("<span class='text-black'>— {$hint}</span>");
        }

        $this->newline();

        if ($state === PromptState::SUBMIT) {
            $this->line("<span class='text-black'>{$valueString}</span>");
            return $this->finalize();
        };

        $this->line("");

        $this->write(<<<HTML
            <div class='space-x-1'>
                {$this->renderSelection($value,$active)}
                <span class='text-black'>/</span>
                {$this->renderSelection(!$value,$inactive)}
            </div>
        HTML);

        return $this->finalize();
    }

    protected function console(): Console
    {
        return $this->console;
    }

    private function renderSelection(bool $selected, string $label): string
    {
        $icon = $selected ? "●" : "○";
        $style = $selected ? "text-green-300" : "text-black";

        return "<span class='{$style}'>{$icon} {$label}</span>";
    }
}
