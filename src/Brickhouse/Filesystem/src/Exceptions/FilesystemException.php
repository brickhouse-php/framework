<?php

namespace Brickhouse\Filesystem\Exceptions;

use Brickhouse\Filesystem\Filesystem;

abstract class FilesystemException extends \Exception
{
    /**
     * Creates a new `FilesystemException`-instance.
     *
     * @param Filesystem    $instance   Filesystem instance which the error occured on.
     * @param string        $path       Path to the relevant element, which caused the exception.
     * @param string        $error      Message which describes the error.
     * @param string        $reason     Reason for why the error occured.
     */
    public function __construct(
        public readonly Filesystem $instance,
        public readonly string $path,
        public readonly string $error,
        public readonly string $reason,
    ) {
        parent::__construct("{$this->error}: {$this->reason} [{$this->path}]");
    }
}
