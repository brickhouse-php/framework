<?php

namespace Brickhouse\Console\Prompts\Renderers;

use Brickhouse\Console\Prompts\Console;
use Brickhouse\Console\Prompts\PromptState;

/**
 * @template TKey of array-key
 * @template TValue
 */
class MultiselectPromptRenderer
{
    use RendersInput;

    public function __construct(protected readonly Console $console)
    {
    }

    /**
     * @param string                    $label
     * @param array<array-key,string>   $selected
     * @param array<TKey,TValue>        $choices
     * @param string                    $active
     * @param PromptState               $state
     * @param string                    $hint
     */
    public function render(
        string $label,
        array $selected,
        array $choices,
        string $active,
        PromptState $state,
        string $hint = ""
    ): string {
        $this->prepare($state);
        $this->label($label);

        if (mb_strlen($hint) > 0) {
            $this->write("<span class='text-black'>— {$hint}</span>");
        }

        $this->newline();

        if ($state === PromptState::SUBMIT) {
            $valueStrings = array_map(
                fn(string $key) => is_string($choices[$key])
                    ? $choices[$key]
                    : $key,
                $selected
            );

            $valueString = join(", ", $valueStrings);

            $this->line("<span class='text-black'>{$valueString}</span>");
            return $this->finalize();
        }

        foreach ($choices as $key => $value) {
            $isActive = $key === $active;
            $isSelected = in_array($key, $selected);

            $icon = $isSelected ? "◼" : "◻";
            $iconStyle = $isSelected ? "text-green-300" : "text-black";
            $textStyle = $isActive ? "" : "text-black";

            $this->line(
                <<<HTML
    <span>
        <span class='{$iconStyle} mr-1'>
            {$icon}
        </span>
        <span class='{$textStyle}'>
            {$value}
        </span>
    </span>
HTML
            );
            $this->newline();
        }

        return $this->finalize();
    }

    protected function console(): Console
    {
        return $this->console;
    }
}
