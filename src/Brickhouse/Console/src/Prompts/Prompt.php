<?php

namespace Brickhouse\Console\Prompts;

use Brickhouse\Console\Prompts\Concerns\HandlesEvents;
use Brickhouse\Console\Prompts\Contracts\HasCursor;
use Brickhouse\Console\Prompts\Contracts\ValidatesInput;
use Brickhouse\Console\Prompts\Terminal\TerminalConsole;

/**
 * @template TValue
 */
abstract class Prompt
{
    use HandlesEvents;

    protected readonly Console $console;

    private Cursor $initialCursor;

    /**
     * The error message of the promptif any.
     *
     * @var null|string
     */
    protected null|string $error = null;

    /**
     * Gets or sets the state of the prompt.
     *
     * @var PromptState
     */
    protected PromptState $state = PromptState::INITIAL;

    /**
     * Gets or sets the content of the current frame.
     *
     * @var null|string
     */
    protected private(set) null|string $frame = null;

    /**
     * Gets or sets the content of the previous frame.
     *
     * @var null|string
     */
    protected private(set) null|string $prevFrame = null;

    /**
     * Constructs a new `Prompt`-instance.
     */
    public function __construct()
    {
        $this->console = new TerminalConsole();
        $this->initialCursor = clone $this->console->cursor;
    }

    /**
     * Render the prompt and return the value supplied by the user.
     *
     * @return TValue
     */
    public function prompt()
    {
        $this->renderPrompt();
        $this->runLoop($this->onKeyPress(...));

        return $this->value();
    }

    /**
     * Request the prompt be submitted back to the user.
     */
    public function submit(): void
    {
        if ($this instanceof ValidatesInput) {
            $validationResult = $this->validate();

            if ($validationResult !== null) {
                $this->state = PromptState::ERROR;
                $this->error = $validationResult;

                return;
            }
        }

        $this->state = PromptState::SUBMIT;
        $this->error = null;
    }

    /**
     * Implementation of the prompt looping mechanism.
     */
    private function runLoop(callable $callable): void
    {
        while (($key = $this->console->read(16)) !== '') {
            $callable($key);

            if (in_array($this->state, [PromptState::CANCEL, PromptState::SUBMIT])) {
                $this->console->writeln("");
                break;
            }
        }
    }

    /**
     * Handler for when a key is pressed.
     *
     * @return void
     */
    private function onKeyPress(string $key): void
    {
        if ($key === Key::CTRL_C) {
            $this->state = PromptState::CANCEL;
        }

        $this->handleKeyPress($key);

        if (in_array($this->state, [PromptState::SUBMIT, PromptState::CANCEL])) {
            $this->emit('finalize');
        }

        $this->renderPrompt();

        if ($this->state === PromptState::CANCEL) {
            exit;
        }
    }

    /**
     * Render the prompt.
     *
     * @return void
     */
    protected function renderPrompt(): void
    {
        $this->console->cursor->hide();

        $this->prevFrame = $this->frame;
        $this->frame = $this->render();

        $this->moveCursorToTemplateStart();
        $this->console->write($this->frame);

        $this->console->cursor->overrideWithActual();
        $this->renderPromptCursor();
    }

    /**
     * Moves the cursor to the beginning of the template.
     *
     * @return void
     */
    private function moveCursorToTemplateStart(): void
    {
        $initial = $this->initialCursor->position;

        $linesInTemplate = count(explode(PHP_EOL, $this->prevFrame));

        // When a template is more than 1 line and the begins rendering on the last line,
        // the initial cursor will be offset by at least one line. This makes the prompt partially render
        // on top of the previous render. So, when the terminal is rendering on the last line, go back (L - 1) lines.
        if ($initial->y + ($linesInTemplate - 1) > $this->console->height && $this->prevFrame !== null) {
            $initial = new CursorPosition(
                $initial->x,
                $initial->y - ($linesInTemplate - 1)
            );
        }

        $this->console->cursor->move($initial);
        $this->console->cursor->clearDown();
    }

    /**
     * Render the cursor in the prompt, if requested.
     *
     * @return void
     */
    private function renderPromptCursor(): void
    {
        if ($this instanceof HasCursor) {
            $currentPosition = $this->console->cursor->position;
            $relativePosition = $this->getCursorPosition();

            $this->console->cursor->move(
                new CursorPosition(
                    $currentPosition->x + $relativePosition->x,
                    $currentPosition->y + $relativePosition->y,
                )
            );

            $this->console->cursor->show();
        }
    }

    /**
     * Gets the value from the prompt.
     *
     * @return TValue
     */
    protected abstract function value();

    /**
     * Render the prompt to the console.
     *
     * @return string
     */
    protected abstract function render(): string;

    /**
     * Handles key press events.
     *
     * @param string    $key
     *
     * @return void
     */
    protected function handleKeyPress(string $key): void {}

    /**
     * Determines whether the given key is an action- or control-key.
     *
     * @param string    $key
     *
     * @return bool
     */
    protected function isActionKey(string $key): bool
    {
        if ($key !== '' && ($key[0] === "\e" || in_array($key, Key::CONTROL_KEYS))) {
            return true;
        }

        return false;
    }
}
