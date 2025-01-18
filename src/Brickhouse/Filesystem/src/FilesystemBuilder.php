<?php

namespace Brickhouse\Filesystem;

interface FilesystemBuilder
{
    /**
     * Builds the file system into a new `Filesystem`-instance.
     *
     * @return Filesystem
     */
    public function build(): Filesystem;
}
