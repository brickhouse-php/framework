<?php

namespace Brickhouse\Console\Prompts;

interface Cursor
{
    /**
     * Gets the current position of the cursor.
     *
     * @var CursorPosition
     */
    public CursorPosition $position { get; }

    /**
     * Hides the cursor.
     *
     * @return self
     */
    public function hide();

    /**
     * Shows the cursor again, if it was hidden.
     *
     * @return self
     */
    public function show();

    /**
     * Move the cursor to the given position in the terminal.
     *
     * @param CursorPosition $position
     *
     * @return self
     */
    public function move(CursorPosition $position);

    /**
     * Move the cursor up by `$lines` amount.
     *
     * @param int   $lines  Amount of lines to move up. Defaults to `1`.
     *
     * @return self
     */
    public function moveUp(int $lines = 1);

    /**
     * Move the cursor down by `$lines` amount.
     *
     * @param int   $lines  Amount of lines to move down. Defaults to `1`.
     *
     * @return self
     */
    public function moveDown(int $lines = 1);

    /**
     * Move the cursor left by `$columns` amount.
     *
     * @param int   $columns    Amount of columns to move left. Defaults to `1`.
     *
     * @return self
     */
    public function moveLeft(int $columns = 1);

    /**
     * Move the cursor right by `$columns` amount.
     *
     * @param int   $columns    Amount of columns to move right. Defaults to `1`.
     *
     * @return self
     */
    public function moveRight(int $columns = 1);

    /**
     * Clear all the content from the cursor and down.
     *
     * @return self
     */
    public function clearDown();

    /**
     * Overrides the internal position with the actual position from the terminal.
     *
     * @return self
     */
    public function overrideWithActual(): self;
}
