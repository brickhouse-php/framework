<?php

namespace Brickhouse\Filesystem\Adapters;

use Brickhouse\Filesystem\Filesystem;
use Brickhouse\Filesystem\FilesystemBuilder;

class MemoryFilesystemBuilder implements FilesystemBuilder
{
    /**
     * @inheritDoc
     */
    public function build(): Filesystem
    {
        $adapter = new \League\Flysystem\InMemory\InMemoryFilesystemAdapter();

        $filesystem = new \League\Flysystem\Filesystem($adapter);

        return new MemoryFilesystem($filesystem);
    }
}
