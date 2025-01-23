<?php

namespace Brickhouse\Process;

use Brickhouse\Process\Exceptions\ProcessStartupException;

/**
 * Process is a wrapper around proc_* functions to easily start sub-processes.
 */
class Process
{
    public const STDIN = 0;
    public const STDOUT = 1;
    public const STDERR = 2;

    public const CHUNK_SIZE = 16 * 1024;

    /**
     * Gets the working directory of the process.
     *
     * @var string
     */
    public readonly string $cwd;

    /**
     * Gets the environment variables to pass to the process.
     *
     * @var array<string,string>
     */
    public readonly array $env;

    /**
     * Defines whether the process is currently running.
     *
     * @var boolean
     */
    protected bool $isRunning = false;

    /**
     * Gets or sets the current process resource.
     *
     * @var null|resource
     */
    protected $process = null;

    /**
     * Gets or sets the current process' `stdin` stream.
     *
     * @var null|resource
     */
    protected $stdin = null;

    /**
     * Gets or sets the current process' `stdout` stream.
     *
     * @var null|resource
     */
    protected $stdout = null;

    /**
     * Gets or sets the current process' `stderr` stream.
     *
     * @var null|resource
     */
    protected $stderr = null;

    /**
     * Creates a new `Process`-instance.
     *
     * @param string|list<string>           $command    Defines the command and it's argument list to execute.
     * @param null|string                   $cwd        The working directory of the process or `null` to use current working directory.
     * @param null|array<string,string>     $env        Environment variables of the process, or `null` to pass the environment of the PHP process.
     * @param mixed                         $input      Input to pass to the process via `stdin`.
     */
    public function __construct(
        public readonly string|array $command,
        null|string $cwd = null,
        null|array $env = null,
        public readonly mixed $input = null,
    ) {
        $this->cwd = $cwd ?? getcwd();
        $this->env = [
            ...getenv(),
            ...($env ?? [])
        ];
    }

    /**
     * Executes the given command and blocks until the process has finished.
     *
     * @param string|list<string>                   $command    Defines the command and it's argument list to execute.
     * @param null|string                           $cwd        The working directory of the process or `null` to use current working directory.
     * @param null|array<string,string>             $env        Environment variables of the process, or `null` to pass the environment of the PHP process.
     * @param mixed                                 $input      Input to pass to the process via `stdin`.
     * @param null|callable(int, string):void       $callback   Callback for when the process outputs text via `stdout` or `stderr`.
     *
     * @return ProcessResult
     */
    public static function execute(
        string|array $command,
        null|string $cwd = null,
        null|array $env = null,
        mixed $input = null,
        ?callable $callback = null,
    ): ProcessResult {
        return new Process(
            $command,
            $cwd,
            $env,
            $input
        )->run($callback);
    }

    /**
     * Runs the process and blocks until the process has finished.
     *
     * @param null|callable(int, string):void       $callback       Callback for when the process outputs text via `stdout` or `stderr`.
     *
     * @return ProcessResult
     */
    public function run(?callable $callback = null): ProcessResult
    {
        try {
            $this->start();

            return $this->wait($callback);
        } catch (ProcessStartupException $e) {
            return new ProcessResult(-1, '', $e->error);
        }
    }

    /**
     * Starts the process and returns after writting the input to `stdin`.
     *
     * @return void
     */
    public function start(): void
    {
        if ($this->isRunning) {
            throw new \RuntimeException("Process is already running.");
        }

        if (!is_dir($this->cwd)) {
            throw new \RuntimeException("Working directory of the process does not exist: {$this->cwd}");
        }

        if ($this->input instanceof \Iterator) {
            $this->input->rewind();
        }

        $descriptors = array(
            self::STDIN => array("pipe", "r"),
            self::STDOUT => array("pipe", "w"),
            self::STDERR => array("pipe", "w"),
        );

        $lastError = 'unknown';
        set_error_handler(function ($type, $msg) use (&$lastError) {
            $lastError = $msg;

            return true;
        });

        try {
            $this->process = @proc_open(
                $this->command,
                $descriptors,
                $pipes,
                $this->cwd,
                $this->env,
                ['bypass_shell' => true]
            );
        } finally {
            restore_error_handler();
        }

        if (!$this->process) {
            throw new ProcessStartupException($this, $lastError);
        }

        $this->stdin = $pipes[self::STDIN];
        $this->stdout = $pipes[self::STDOUT];
        $this->stderr = $pipes[self::STDERR];
        $this->isRunning = true;

        $this->writeInput();
    }

    /**
     * Waits for the process to terminate and returns it's exit code.
     *
     * @param null|callable(int, string):void   $callback       Callback for when the process outputs text via `stdout` or `stderr`.
     *
     * @return ProcessResult
     */
    public function wait(null|callable $callback = null): ProcessResult
    {
        $callback ??= function () {};

        stream_set_blocking($this->stdout, false);
        stream_set_blocking($this->stderr, false);

        $stdout = '';
        $stderr = '';

        do {
            $running = proc_get_status($this->process)['running'];

            if (($chunk = $this->readStreamOutput($this->stdout)) !== '') {
                $stdout .= $chunk;
                $callback(self::STDOUT, $chunk);
            }

            if (($chunk = $this->readStreamOutput($this->stderr)) !== '') {
                $stderr .= $chunk;
                $callback(self::STDERR, $chunk);
            }

            usleep(1000);
        } while ($running);

        fclose($this->stdin);
        fclose($this->stdout);
        fclose($this->stderr);

        $exitCode = proc_close($this->process);

        return new ProcessResult($exitCode, $stdout, $stderr);
    }

    /**
     * Terminates the process prematurely.
     *
     * @return bool
     */
    public function terminate(): bool
    {
        $result = proc_terminate($this->process);

        $this->process = null;
        $this->stdin = null;
        $this->stdout = null;
        $this->stderr = null;

        return $result;
    }

    /**
     * Writes the current input to the process.
     *
     * @return void
     */
    protected function writeInput(): void
    {
        if ($this->input instanceof \Iterator) {
            foreach ($this->input as $chunk) {
                fwrite($this->stdin, $chunk);
            }
            return;
        }

        if (is_string($this->input)) {
            fwrite($this->stdin, $this->input);
            return;
        }

        if ($this->input instanceof \Stringable) {
            fwrite($this->stdin, $this->input->__toString());
            return;
        }

        fwrite($this->stdin, (string) $this->input);
    }

    /**
     * Reads all the output from the given resource until it is exhaused.
     *
     * @param resource  $stream
     *
     * @return string
     */
    protected function readStreamOutput($stream): string
    {
        $buffer = '';

        while (true) {
            $data = @fread($stream, self::CHUNK_SIZE);
            $buffer .= $data;

            if ($data === '') {
                break;
            }
        }

        return $buffer;
    }
}
