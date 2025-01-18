<?php

namespace Brickhouse\Console\Concerns;

use Symfony\Component\Console\Output\ConsoleOutput;

trait WritesToConsole
{
    private static null|ConsoleOutput $outputInterface = null;

    private ConsoleOutput $output {
        get {
            return self::$outputInterface ??= new ConsoleOutput();
        }
    }

    /**
     * Prints the given message to the console output.
     *
     * @param list<string>|string $content
     *
     * @return void
     */
    public function writeHtml(array|string $content, bool $newline = true): void
    {
        $content = join("", array_wrap($content));
        $content = \Termwind\parse($content);

        if ($newline) {
            $this->output->writeln($content);
        } else {
            $this->output->write($content);
        }
    }

    /**
     * Prints a newline.
     *
     * @return void
     */
    public function newline(): void
    {
        $this->output->write(PHP_EOL);
    }

    /**
     * Prints the given message to the console output.
     *
     * @param string $message
     *
     * @return void
     */
    public function write(string $message): void
    {
        $this->output->write($message);
    }

    /**
     * Prints the given message to the console output.
     *
     * @param string $message
     *
     * @return void
     */
    public function writeln(string $message): void
    {
        $this->output->writeln($message);
    }

    /**
     * Prints the given message to the console output.
     *
     * @param string $message
     * @return void
     */
    public function debug(string $message, bool $newline = true): void
    {
        $this->writeHtml(<<<HTML
            <span class='ml-2'>
                <span class='uppercase px-1 mr-1 bg-cyan-500 text-white'>
                    DBUG
                </span>
                {$message}
            </span>
        HTML, $newline);
    }

    /**
     * Prints the given message to the console output.
     *
     * @param string $message
     * @return void
     */
    public function info(string $message, bool $newline = true): void
    {
        $this->writeHtml(<<<HTML
            <span class='ml-2'>
                <span class='uppercase px-1 mr-1 bg-blue-500 text-white'>
                    INFO
                </span>
                {$message}
            </span>
        HTML, $newline);
    }

    /**
     * Prints the given message to the console output.
     *
     * @param string $message
     * @return void
     */
    public function notice(string $message, bool $newline = true): void
    {
        $this->writeHtml(<<<HTML
            <span class='ml-2'>
                <span class='uppercase px-1 mr-1 bg-cyan-400 text-white'>
                    NOTI
                </span>
                {$message}
            </span>
        HTML, $newline);
    }

    /**
     * Prints the given message to the console output.
     *
     * @param string $message
     * @return void
     */
    public function warning(string $message, bool $newline = true): void
    {
        $this->writeHtml(<<<HTML
            <span class='ml-2'>
                <span class='uppercase px-1 mr-1 bg-amber-400 text-white'>
                    WARN
                </span>
                {$message}
            </span>
        HTML, $newline);
    }

    /**
     * Prints the given message to the console output.
     *
     * @param string $message
     * @return void
     */
    public function error(string $message, bool $newline = true): void
    {
        $this->writeHtml(<<<HTML
            <span class='ml-2'>
                <span class='uppercase px-1 mr-1 bg-red-400 text-white'>
                    ERRO
                </span>
                {$message}
            </span>
        HTML, $newline);
    }

    /**
     * Prints the given message to the console output.
     *
     * @param string $message
     * @return void
     */
    public function critical(string $message, bool $newline = true): void
    {
        $this->writeHtml(<<<HTML
            <span class='ml-2'>
                <span class='uppercase px-1 mr-1 bg-red-600 text-white'>
                    CRIT
                </span>
                {$message}
            </span>
        HTML, $newline);
    }

    /**
     * Prints the given message to the console output.
     *
     * @param string $message
     * @return void
     */
    public function alert(string $message, bool $newline = true): void
    {
        $this->writeHtml(<<<HTML
            <span class='ml-2'>
                <span class='uppercase px-1 mr-1 bg-red-800 text-white'>
                    ALRT
                </span>
                {$message}
            </span>
        HTML, $newline);
    }

    /**
     * Prints the given message to the console output.
     *
     * @param string $message
     * @return void
     */
    public function emergency(string $message, bool $newline = true): void
    {
        $this->writeHtml(<<<HTML
            <span class='ml-2'>
                <span class='uppercase px-1 mr-1 bg-pink-700 text-white'>
                    EMRG
                </span>
                {$message}
            </span>
        HTML, $newline);
    }

    /**
     * Prints the given message to the console output.
     *
     * @param string $message
     * @return void
     */
    public function comment(string $message): void
    {
        $this->writeHtml(<<<HTML
            <span class='ml-4 my-1 font-bold'>
                {$message}
            </span>
        HTML);
    }
}
