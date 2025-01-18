<?php

namespace Brickhouse\Console\Prompts\Renderers;

use Brickhouse\Console\Prompts\Console;
use Brickhouse\Console\Prompts\PromptState;

trait RendersInput
{
    private const string BORDER = '│';

    private string $inputBuffer = "";

    protected PromptState $state;

    protected function line(string $html): void
    {
        $icon = self::BORDER;
        $theme = $this->theme();

        // Termwind only allows pre-formatted text on `pre`-tags, but they also add 3 spaces in front of it's
        // content, so we have to remove it again.
        $markdown = \Termwind\parse("<div><pre class='{$theme} mr-2'>{$icon}</pre>{$html}</div>");
        $markdown = preg_replace("/[ ]{3}/", "", $markdown, limit: 1);

        // We also have to remove a single newline, which is added by Termwind on non-`span` tags.
        if (str_starts_with($markdown, "\n")) {
            $markdown = substr($markdown, 1);
        }

        $this->inputBuffer .= $markdown;
    }

    protected function write(string $html): void
    {
        $rendered = \Termwind\parse($html);
        $this->inputBuffer .= $rendered;
    }

    protected function writeln(string $html): void
    {
        $this->write($html);
        $this->newline();
    }

    protected function newline(): void
    {
        $this->inputBuffer .= PHP_EOL;
    }

    protected function label(string $label): void
    {
        $icon = $this->icon();
        $theme = $this->theme();

        $this->write("<span class='{$theme} mr-2'>{$icon}</span>");
        $this->write("<span class='font-bold mr-1'>{$label}</span>");
    }

    protected function prepare(PromptState $state): void
    {
        $this->state = $state;
        $this->inputBuffer = '';
    }

    protected function finalize(): string
    {
        return $this->inputBuffer;
    }

    public function theme(): string
    {
        // @phpstan-ignore function.alreadyNarrowedType
        if (method_exists($this, "getTheme")) {
            if (($theme = $this->getTheme()) !== null) {
                return $theme;
            }
        }

        return match ($this->state) {
            PromptState::INITIAL => "text-indigo-500",
            PromptState::ACTIVE => "text-indigo-500",
            PromptState::SUBMIT => "text-green-300",
            PromptState::ERROR => "text-amber-200",
            PromptState::CANCEL => "text-red-300",
        };
    }

    protected function icon(): string
    {
        // @phpstan-ignore function.alreadyNarrowedType
        if (method_exists($this, "getIcon")) {
            if (($icon = $this->getIcon()) !== null) {
                return $icon;
            }
        }

        return match ($this->state) {
            PromptState::INITIAL => "◆",
            PromptState::ACTIVE => "◆",
            PromptState::SUBMIT => "◇",
            PromptState::ERROR => "▲",
            PromptState::CANCEL => "■",
        };
    }

    /**
     * Parses the given text as selected and return it's style.
     */
    protected function selected(string $text): string
    {
        return "<span class='underline text-indigo-300'>{$text}</span>";
    }

    /**
     * Parses the given text as muted text and return it's style.
     */
    protected function muted(string $text): string
    {
        return "<span class='text-black'>{$text}</span>";
    }

    protected abstract function console(): Console;
}
