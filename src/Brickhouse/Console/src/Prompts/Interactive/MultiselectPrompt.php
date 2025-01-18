<?php

namespace Brickhouse\Console\Prompts\Interactive;

use Brickhouse\Console\Prompts\Contracts\ValidatesInput;
use Brickhouse\Console\Prompts\Key;
use Brickhouse\Console\Prompts\Prompt;
use Brickhouse\Console\Prompts\Renderers\MultiselectPromptRenderer;

/**
 * @template TKey of array-key
 * @template TValue
 *
 * @phpstan-extends Prompt<TValue[]>
 */
class MultiselectPrompt extends Prompt implements ValidatesInput
{
    /** @var MultiselectPromptRenderer<TKey,TValue> $renderer */
    protected readonly MultiselectPromptRenderer $renderer;

    /**
     * @var array<string,TValue>
     */
    public readonly array $choices;

    /**
     * Contains all the selected choices.
     *
     * @var list<string>
     */
    protected array $selected;

    /**
     * Contains the hovered choice.
     *
     * @var string
     */
    protected string $active;

    /**
     * @param string                $label
     * @param array<TKey,TValue>    $choices
     * @param list<string>          $initial
     * @param string                $hint
     */
    public function __construct(
        public readonly string $label,
        array $choices,
        public readonly array $initial = [],
        public readonly string $hint = "",
        public readonly bool|string $required = false
    ) {
        parent::__construct();

        $this->choices = $this->normalizeChoices($choices);
        $this->active = key($this->choices);
        $this->selected = $initial;

        $this->renderer = new MultiselectPromptRenderer($this->console);
    }

    protected function value()
    {
        return array_map(
            fn(string $key) => $this->choices[$key],
            $this->selected
        );
    }

    protected function render(): string
    {
        return $this->renderer->render(
            $this->label,
            $this->selected,
            $this->choices,
            $this->active,
            $this->state,
            $this->hint
        );
    }

    public function validate(): null|string
    {
        if (!$this->required || !empty($this->selected)) {
            return null;
        }

        if (is_string($this->required)) {
            return $this->required;
        }

        return "Please select an option.";
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
            $this->active = array_prev_key(
                $this->choices,
                $this->active,
                wrap: true
            );
        }

        if ($key === Key::DOWN_ARROW) {
            $this->active = array_next_key(
                $this->choices,
                $this->active,
                wrap: true
            );
        }

        if ($key === Key::LEFT_ARROW) {
            $this->unselectActiveItem();
        }

        if ($key === Key::RIGHT_ARROW) {
            $this->selectActiveItem();
        }

        if ($key === Key::SPACE) {
            $this->toggleActiveItem();
        }
    }

    protected function toggleActiveItem(): void
    {
        if ($this->isActiveItemSelected()) {
            $this->unselectActiveItem();
        } else {
            $this->selectActiveItem();
        }
    }

    protected function isActiveItemSelected(): bool
    {
        return in_array($this->active, $this->selected, strict: true);
    }

    protected function selectActiveItem(): void
    {
        $this->selected[] = $this->active;
    }

    protected function unselectActiveItem(): void
    {
        if (($key = array_search($this->active, $this->selected)) !== false) {
            unset($this->selected[$key]);
        }
    }
}
