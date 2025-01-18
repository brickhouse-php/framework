<?php

namespace Brickhouse\Console\Prompts\Interactive;

use Brickhouse\Console\Prompts\Contracts\HasCursor;
use Brickhouse\Console\Prompts\Contracts\ValidatesInput;
use Brickhouse\Console\Prompts\CursorPosition;
use Brickhouse\Console\Prompts\Key;
use Brickhouse\Console\Prompts\Prompt;
use Brickhouse\Console\Prompts\Renderers\TextPromptRenderer;

/**
 * @phpstan-extends Prompt<string>
 */
class TextPrompt extends Prompt implements HasCursor, ValidatesInput
{
    protected readonly TextPromptRenderer $renderer;

    protected string $value = '';
    protected int $cursorPosition = 0;

    public function __construct(
        public readonly string $label,
        public readonly string $placeholder = '',
        public readonly string $initial = '',
        public readonly string $hint = '',
        public readonly bool|string $required = false,
    ) {
        parent::__construct();

        $this->value = $initial;
        $this->cursorPosition = mb_strlen($this->value);

        $this->renderer = new TextPromptRenderer($this->console);

        $this->on('finalize', function () {
            if (empty($this->value) && !empty($this->placeholder)) {
                $this->value = $this->placeholder;
            }
        });
    }

    protected function value()
    {
        return $this->value;
    }

    public function validate(): null|string
    {
        if (!$this->required || !empty($this->value)) {
            return null;
        }

        if (is_string($this->required)) {
            return $this->required;
        }

        return "Please enter a value.";
    }

    protected function render(): string
    {
        return $this->renderer->render(
            $this->label,
            $this->value,
            $this->state,
            $this->placeholder,
            $this->hint,
            $this->error,
        );
    }

    protected function handleKeyPress(string $key): void
    {
        if (!$this->isActionKey($key)) {
            if ($key === Key::ENTER) {
                $this->submit();
                return;
            }

            $this->value =
                mb_substr($this->value, 0, $this->cursorPosition) .
                $key .
                mb_substr($this->value, $this->cursorPosition);

            $this->cursorPosition += mb_strlen($key);
            return;
        }

        if ($key === Key::BACKSPACE) {
            if ($this->cursorPosition === 0) {
                return;
            }

            $this->value = join([
                mb_substr($this->value, 0, $this->cursorPosition - 1),
                mb_substr($this->value, $this->cursorPosition)
            ]);
            $this->cursorPosition--;
        }

        if ($key === Key::DELETE) {
            if ($this->cursorPosition >= mb_strlen($this->value)) {
                return;
            }

            $this->value = join([
                mb_substr($this->value, 0, $this->cursorPosition),
                mb_substr($this->value, $this->cursorPosition + 1)
            ]);
        }

        if ($key === Key::LEFT_ARROW && $this->cursorPosition > 0) {
            $this->cursorPosition--;
        }

        if ($key === Key::RIGHT_ARROW && $this->cursorPosition < mb_strlen($this->value)) {
            $this->cursorPosition++;
        }
    }

    public function getCursorPosition(): CursorPosition
    {
        if (empty($this->value) && !empty($this->placeholder)) {
            return new CursorPosition(-mb_strlen($this->placeholder), 0);
        }

        return new CursorPosition($this->cursorPosition - mb_strlen($this->value), 0);
    }
}
