<?php

namespace Brickhouse\Process\Exceptions;

use Brickhouse\Process\Process;

class ProcessStartupException extends \RuntimeException
{
    public function __construct(
        public readonly Process $process,
        public readonly string $error,
    ) {
        $message = "Process failed to start: {$error}\nCommand: " . join(" ", $process->command);

        parent::__construct($message, -1);
    }
}
