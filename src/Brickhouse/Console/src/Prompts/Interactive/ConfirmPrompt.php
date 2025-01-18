<?php

namespace Brickhouse\Console\Prompts\Interactive;

use Brickhouse\Console\Prompts\Key;
use Brickhouse\Console\Prompts\Prompt;
use Brickhouse\Console\Prompts\Renderers\ConfirmPromptRenderer;

/**
 * @phpstan-extends Prompt<bool>
 */
class ConfirmPrompt extends Prompt
{
    protected readonly ConfirmPromptRenderer $renderer;

    protected bool $selected = false;

    public function __construct(
        public readonly string $label,
        public readonly bool $initial = false,
        public readonly string $active = 'Yes',
        public readonly string $inactive = 'No',
        public readonly string $hint = '',
    ) {
        parent::__construct();

        $this->selected = $initial;
        $this->renderer = new ConfirmPromptRenderer($this->console);
    }

    protected function value()
    {
        return $this->selected;
    }

    protected function render(): string
    {
        return $this->renderer->render(
            $this->label,
            $this->selected,
            $this->state,
            $this->active,
            $this->inactive,
            $this->hint,
        );
    }

    protected function handleKeyPress(string $key): void
    {
        if ($key === Key::ENTER) {
            $this->submit();
            return;
        }

        if (in_array($key, [Key::LEFT_ARROW, Key::RIGHT_ARROW, Key::SPACE, Key::TAB])) {
            $this->selected = !$this->selected;
        }
    }
}
