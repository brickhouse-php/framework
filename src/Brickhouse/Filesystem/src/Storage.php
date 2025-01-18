<?php

namespace Brickhouse\Filesystem;

use Brickhouse\Filesystem\Adapters;

class Storage
{
    /**
     * Creates a new builder for local file system adapters.
     *
     * @param string    $path       Defines the path to the local storage.
     *
     * @return Adapters\LocalFilesystemBuilder
     */
    public final static function local(string $path): Adapters\LocalFilesystemBuilder
    {
        return new Adapters\LocalFilesystemBuilder($path);
    }

    /**
     * Creates a new builder for in-memory file system adapters.
     *
     * @return Adapters\MemoryFilesystemBuilder
     */
    public final static function memory(): Adapters\MemoryFilesystemBuilder
    {
        return new Adapters\MemoryFilesystemBuilder();
    }
}
