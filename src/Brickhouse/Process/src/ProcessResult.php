<?php

namespace Brickhouse\Process;

use Brickhouse\Process\Exceptions\ProcessStartupException;

/**
 * Process is a wrapper around proc_* functions to easily start sub-processes.
 */
class ProcessResult
{
    /**
     * Creates a new `ProcessResult`-instance.
     *
     * @param int       $exitCode
     * @param string    $stdout
     * @param string    $stderr
     */
    public function __construct(
        public readonly int $exitCode,
        public readonly string $stdout,
        public readonly string $stderr,
    ) {}

    /**
     * Gets wheter the process result contains a zero-exit code.
     *
     * @return boolean
     */
    public function isSuccessful(): bool
    {
        return $this->exitCode === 0;
    }
}
