<?php

namespace Brickhouse\Console\Prompts\Interactive;

use Brickhouse\Console\Prompts\Key;
use Brickhouse\Console\Prompts\Prompt;
use Brickhouse\Console\Prompts\Renderers\SelectPromptRenderer;

/**
 * @template TKey of array-key
 * @template TValue
 *
 * @phpstan-extends Prompt<TValue>
 */
class SelectPrompt extends Prompt
{
    /** @var SelectPromptRenderer<TKey,TValue> $renderer */
    protected readonly SelectPromptRenderer $renderer;

    /**
     * @var array<string,TValue>
     */
    public readonly array $choices;

    protected string $selected;

    /**
     * @param string                $label
     * @param array<TKey,TValue>    $choices
     * @param null|string           $initial
     * @param string                $hint
     */
    public function __construct(
        public readonly string $label,
        array $choices,
        public readonly null|string $initial = null,
        public readonly string $hint = ""
    ) {
        parent::__construct();

        $this->choices = $this->normalizeChoices($choices);
        $this->selected = $initial ?? key($this->choices);

        $this->renderer = new SelectPromptRenderer($this->console);
    }

    protected function value()
    {
        return $this->choices[$this->selected];
    }

    protected function render(): string
    {
        return $this->renderer->render(
            $this->label,
            $this->selected,
            $this->choices,
            $this->state,
            $this->hint
        );
    }

    /**
     * Normalize the given choices to use string keys.
     *
     * @param array<TKey,TValue>    $choices
     *
     * @return array<string,TValue>
     */
    protected function normalizeChoices(array $choices): array
    {
        $normalized = [];

        foreach ($choices as $key => $value) {
            if (is_string($key)) {
                $normalized[$key] = $value;
                continue;
            }

            if (is_string($value)) {
                $normalized[$value] = $value;
                continue;
            }

            throw new \RuntimeException(
                "Choices must be an associative array with string keys or a string list."
            );
        }

        return $normalized;
    }

    protected function handleKeyPress(string $key): void
    {
        if ($key === Key::ENTER) {
            $this->submit();
        }

        if ($key === Key::UP_ARROW) {
            $this->selected = array_prev_key(
                $this->choices,
                $this->selected,
                wrap: true
            );
        }

        if ($key === Key::DOWN_ARROW) {
            $this->selected = array_next_key(
                $this->choices,
                $this->selected,
                wrap: true
            );
        }
    }
}
