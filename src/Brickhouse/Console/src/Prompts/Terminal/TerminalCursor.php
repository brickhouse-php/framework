<?php

namespace Brickhouse\Console\Prompts\Terminal;

use Brickhouse\Console\Prompts\CursorPosition;

class TerminalCursor implements \Brickhouse\Console\Prompts\Cursor
{
    /**
     * Gets the current position of the cursor.
     *
     * @var CursorPosition
     */
    public protected(set) CursorPosition $position;

    public function __construct(
        protected readonly TerminalConsole $console,
    ) {
        $this->overrideWithActual();
    }

    /**
     * @inheritDoc
     */
    public function hide(): self
    {
        $this->console->write("\e[?25l");
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function show(): self
    {
        $this->console->write("\e[?25h");
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function move(CursorPosition $position): self
    {
        $position = new CursorPosition(
            x: min($this->console->width, max(1, $position->x)),
            y: min($this->console->height, max(1, $position->y)),
        );

        $this->console->write("\e[{$position->y};{$position->x}H");
        $this->position = $position;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function moveUp(int $lines = 1): self
    {
        return $this->move(
            new CursorPosition(
                $this->position->x,
                $this->position->y - $lines
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function moveDown(int $lines = 1): self
    {
        return $this->move(
            new CursorPosition(
                $this->position->x,
                $this->position->y + $lines
            )
        );
    }
    /**
     * @inheritDoc
     */
    public function moveLeft(int $columns = 1): self
    {
        return $this->move(
            new CursorPosition(
                $this->position->x - $columns,
                $this->position->y
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function moveRight(int $columns = 1): self
    {
        return $this->move(
            new CursorPosition(
                $this->position->x + $columns,
                $this->position->y
            )
        );
    }

    /**
     * @inheritDoc
     */
    public function clearDown(): self
    {
        $this->console->write("\e[0J");
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function overrideWithActual(): self
    {
        $this->position = $this->getActualPosition();
        return $this;
    }

    private function getActualPosition(): CursorPosition
    {
        $this->console->write("\e[6n");

        preg_match('/(?<y>[\d]+);(?<x>[\d]+)/', $this->console->read(64), $matches);

        return new CursorPosition(
            x: (int) ($matches['x'] ?? 80),
            y: (int) ($matches['y'] ?? 25)
        );
    }
}
