<?php

namespace Brickhouse\Console\Prompts\Renderers;

use Brickhouse\Console\Prompts\Console;
use Brickhouse\Console\Prompts\PromptState;

/**
 * @template TKey of array-key
 * @template TValue
 */
class SelectPromptRenderer
{
    use RendersInput;

    public function __construct(protected readonly Console $console)
    {
    }

    /**
     * @param string                $label
     * @param string                $selected
     * @param array<TKey,TValue>    $choices
     * @param PromptState           $state
     * @param string                $hint
     */
    public function render(
        string $label,
        string $selected,
        array $choices,
        PromptState $state,
        string $hint = ""
    ): string {
        $value = $choices[$selected];

        $this->prepare($state);
        $this->label($label);

        if (mb_strlen($hint) > 0) {
            $this->write("<span class='text-black'>— {$hint}</span>");
        }

        $this->newline();

        if ($state === PromptState::SUBMIT) {
            $this->line("<span class='text-black'>{$value}</span>");
            return $this->finalize();
        }

        foreach ($choices as $key => $value) {
            $isSelected = $selected === $key;

            $icon = $isSelected ? "●" : "○";
            $style = $isSelected ? "text-green-300" : "text-black";

            $this->line("<span class='{$style}'>{$icon} {$value}</span>");
            $this->newline();
        }

        return $this->finalize();
    }

    protected function console(): Console
    {
        return $this->console;
    }
}
