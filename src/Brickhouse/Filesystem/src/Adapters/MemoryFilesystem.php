<?php

namespace Brickhouse\Filesystem\Adapters;

class MemoryFilesystem extends FlyFilesystem
{
    /**
     * Creates a new in-memory filesystem adapter.
     *
     * @param \League\Flysystem\Filesystem  $filesystem     Gets the underlying Flysystem instance of the file system..
     */
    public function __construct(
        \League\Flysystem\Filesystem $filesystem,
    ) {
        parent::__construct($filesystem);
    }
}
