<?php

namespace Brickhouse\Console\Prompts\Interactive;

use Brickhouse\Console\Prompts\Prompt;
use Brickhouse\Console\Prompts\PromptState;
use Brickhouse\Console\Prompts\Renderers\SpinnerRenderer;

/**
 * @template TReturn
 *
 * @phpstan-extends Prompt<TReturn>
 */
class Spinner extends Prompt
{
    protected readonly SpinnerRenderer $renderer;

    /**
     * The process ID after forking.
     */
    protected int $pid;

    /**
     * @param string                $label
     * @param \Closure():TReturn    $callback
     * @param bool                  $dots
     */
    public function __construct(
        public readonly string $label,
        public readonly \Closure $callback,
        public readonly bool $dots = true
    ) {
        parent::__construct();

        $this->renderer = new SpinnerRenderer($this->console);
    }

    public function __destruct()
    {
        if (!empty($this->pid)) {
            posix_kill($this->pid, SIGINT);
        }
    }

    protected function value()
    {
        throw new \RuntimeException("Spinner::value is not meant to be executed.");
    }

    /**
     * Start the spinner.
     *
     * @return TReturn
     */
    public function spin()
    {
        if (!function_exists("pcntl_fork")) {
            return $this->spinStatically();
        }

        $originalAsync = pcntl_async_signals(true);
        pcntl_signal(SIGINT, fn() => $this->submit());

        try {
            $this->renderPrompt();

            $this->pid = pcntl_fork();

            if ($this->pid === 0) {
                while (true) {
                    $this->renderPrompt();

                    if (in_array($this->state, [PromptState::CANCEL, PromptState::SUBMIT])) {
                        break;
                    }

                    usleep(80 * 1000);
                }

                return null;
            } else {
                $result = ($this->callback)();

                $this->resetTerminal($originalAsync);

                return $result;
            }
        } catch (\Throwable $e) {
            $this->resetTerminal($originalAsync);

            throw $e;
        }
    }

    /**
     * Start the spinner in a static fashion.
     *
     * @return TReturn
     */
    protected function spinStatically()
    {
        $this->renderPrompt();
        return ($this->callback)();
    }

    /**
     * Reset the terminal.
     */
    protected function resetTerminal(bool $originalAsync): void
    {
        pcntl_async_signals($originalAsync);
        pcntl_signal(SIGINT, SIG_DFL);
    }

    protected function render(): string
    {
        return $this->renderer->render($this->label, $this->state, $this->dots);
    }
}
