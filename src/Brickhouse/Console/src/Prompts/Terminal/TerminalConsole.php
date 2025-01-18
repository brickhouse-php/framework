<?php

namespace Brickhouse\Console\Prompts\Terminal;

use Brickhouse\Console\Prompts\Cursor;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

class TerminalConsole implements \Brickhouse\Console\Prompts\Console
{
    /**
     * Gets or sets the initial TTY mode.
     */
    private ?string $initialTtyMode = null;

    /**
     * Gets the output interface for console output.
     *
     * @var OutputInterface
     */
    private readonly OutputInterface $output;

    /**
     * Gets the cursor interface for the terminal.
     *
     * @var Cursor
     */
    public readonly Cursor $cursor;

    /**
     * @inheritDoc
     */
    public int $width {
        get => \Termwind\terminal()->width();
    }

    /**
     * @inheritDoc
     */
    public int $height {
        get => \Termwind\terminal()->height();
    }

    public function __construct()
    {
        $this->switchToInteractiveMode();

        $this->output = new ConsoleOutput();
        $this->cursor = new TerminalCursor($this);
    }

    public function __destruct()
    {
        $this->restoreOriginalMode();
    }

    /**
     * Switches the terminal to 'interactive' mode.
     *
     * @return void
     */
    protected function switchToInteractiveMode(): void
    {
        $this->initialTtyMode ??= $this->exec('stty -g');
        $this->exec("stty -echo -icanon");
    }

    /**
     * Restores the terminal back to it's original mode.
     *
     * @return void
     */
    protected function restoreOriginalMode(): void
    {
        if ($this->initialTtyMode !== null) {
            $this->exec("stty {$this->initialTtyMode}");
        }

        $this->exec("stty echo icanon");

        $this->cursor->show();
        $this->writeln();
    }

    /**
     * @inheritDoc
     */
    public function read(int $bytes): string
    {
        return fread(STDIN, $bytes);
    }

    /**
     * @inheritDoc
     */
    public function readln(): string
    {
        return fgets(STDIN);
    }

    /**
     * @inheritDoc
     */
    public function write(string $content): void
    {
        $this->output->write($content);
    }

    /**
     * @inheritDoc
     */
    public function writeln(string $content = ''): void
    {
        $this->output->writeln($content);
    }

    /**
     * @inheritDoc
     */
    public function exec(
        string $command,
        array $arguments = [],
        null|string &$stdout = null,
        null|string &$stderr = null
    ): int {
        $process = proc_open($command, [
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ], $pipes);

        if (! $process) {
            throw new \RuntimeException('Failed to create process.');
        }

        $stdout = stream_get_contents($pipes[1]) ?: null;
        $stderr = stream_get_contents($pipes[2]) ?: null;
        $code = proc_close($process);

        return $code;
    }
}
