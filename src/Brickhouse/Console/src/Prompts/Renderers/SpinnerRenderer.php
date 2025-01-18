<?php

namespace Brickhouse\Console\Prompts\Renderers;

use Brickhouse\Console\Prompts\Console;
use Brickhouse\Console\Prompts\PromptState;

class SpinnerRenderer
{
    use RendersInput;

    public const array DOTS = [
        '◒',
        '◐',
        '◓',
        '◑'
    ];

    protected int $counter = 0;

    public function __construct(
        protected readonly Console $console
    ) {}

    public function render(
        string $label,
        PromptState $state,
        bool $dots = true,
    ): string {
        if ($dots) {
            $appendix = str_repeat(".", (int) ceil(($this->counter % 15) / 5));
            $label .= $appendix;
        }

        $this->prepare($state);
        $this->label($label);

        return $this->finalize();
    }

    protected function console(): Console
    {
        return $this->console;
    }

    public function getTheme(): null|string
    {
        if ($this->state === PromptState::INITIAL) {
            return "text-purple-400";
        }

        return null;
    }

    public function getIcon(): null|string
    {
        if ($this->state === PromptState::INITIAL) {
            return self::DOTS[($this->counter++) % count(self::DOTS)];
        }

        return null;
    }
}
