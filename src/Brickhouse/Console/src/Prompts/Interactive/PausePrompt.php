<?php

namespace Brickhouse\Console\Prompts\Interactive;

use Brickhouse\Console\Prompts\Key;
use Brickhouse\Console\Prompts\Prompt;
use Brickhouse\Console\Prompts\Renderers\PausePromptRenderer;

/**
 * @phpstan-extends Prompt<bool>
 */
class PausePrompt extends Prompt
{
    protected readonly PausePromptRenderer $renderer;

    /**
     * @param string                $label
     */
    public function __construct(
        public readonly string $label,
    ) {
        parent::__construct();

        $this->renderer = new PausePromptRenderer($this->console);
    }

    protected function value()
    {
        return true;
    }

    protected function render(): string
    {
        return $this->renderer->render(
            $this->label,
            $this->state,
        );
    }

    protected function handleKeyPress(string $key): void
    {
        if (!$this->isActionKey($key)) {
            $this->submit();
        }
    }
}
