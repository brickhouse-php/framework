<?php

namespace Brickhouse\Console\Prompts;

interface Console
{
    /**
     * Gets the current width of the terminal, in columns.
     *
     * @var int
     */
    public int $width { get; }

    /**
     * Gets the current height of the terminal, in lines.
     *
     * @var int
     */
    public int $height { get; }

    /**
     * Gets the associated cursor with the console.
     *
     * @var Cursor
     */
    public Cursor $cursor { get; }

    /**
     * Reads the next `$bytes` bytes from the terminal and return it.
     *
     * @param int   $bytes
     *
     * @return string
     */
    public function read(int $bytes): string;

    /**
     * Reads the next line from the terminal and return it.
     *
     * @return string
     */
    public function readln(): string;

    /**
     * Writes the given content to the terminal.
     *
     * @param string    $content
     *
     * @return void
     */
    public function write(string $content): void;

    /**
     * Writes the given content to the terminal, followed by a newline.
     *
     * @param string    $content
     *
     * @return void
     */
    public function writeln(string $content): void;

    /**
     * Executes the given command.
     *
     * @param string        $command
     * @param list<mixed>   $arguments
     * @param null|string   $stdout
     * @param null|string   $stderr
     *
     * @return int
     */
    public function exec(
        string $command,
        array $arguments = [],
        null|string &$stdout = null,
        null|string &$stderr = null
    ): int;
}
