<?php

namespace Brickhouse\Filesystem\Adapters;

use Brickhouse\Filesystem\Exceptions;
use Brickhouse\Filesystem\Filesystem;

/**
 * Encapsulation of a filesystem, which internally uses `league/flysystem`.
 */
abstract class FlyFilesystem implements Filesystem
{
    /**
     * Creates a new filesystem adapter using a Flysystem filesystem.
     *
     * @param \League\Flysystem\Filesystem  $filesystem     Gets the underlying Flysystem instance of the file system..
     */
    public function __construct(
        protected readonly \League\Flysystem\Filesystem $filesystem,
    ) {}

    /**
     * @inheritDoc
     */
    public function read(string $path): string
    {
        try {
            return $this->filesystem->read($path);
        } catch (\League\Flysystem\UnableToReadFile $e) {
            throw new Exceptions\ReadFailedException($this, $path, $e->reason());
        } catch (\League\Flysystem\FilesystemException $e) {
            throw new Exceptions\ReadFailedException($this, $path, $e->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function write(string $path, string $contents): void
    {
        try {
            $this->filesystem->write($path, $contents);
        } catch (\League\Flysystem\UnableToWriteFile $e) {
            throw new Exceptions\WriteFailedException($this, $path, $e->reason());
        } catch (\League\Flysystem\FilesystemException $e) {
            throw new Exceptions\WriteFailedException($this, $path, $e->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function append(string $path, string $contents): void
    {
        try {
            $existingContent = $this->read($path);
        } catch (Exceptions\ReadFailedException) {
            $existingContent = '';
        }

        $existingContent .= $contents;

        $this->write($path, $existingContent);
    }

    /**
     * @inheritDoc
     */
    public function delete(string $path): void
    {
        try {
            $this->filesystem->delete($path);
        } catch (\League\Flysystem\UnableToDeleteFile $e) {
            throw new Exceptions\DeleteFailedException($this, $path, $e->reason());
        } catch (\League\Flysystem\FilesystemException $e) {
            throw new Exceptions\DeleteFailedException($this, $path, $e->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function exists(string $path): bool
    {
        try {
            return $this->filesystem->fileExists($path);
        } catch (\League\Flysystem\UnableToCheckExistence) {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function createDirectory(string $path): void
    {
        try {
            $this->filesystem->createDirectory($path);
        } catch (\League\Flysystem\UnableToCreateDirectory $e) {
            throw new Exceptions\CreateDirectoryFailedException($this, $path, $e->reason());
        } catch (\League\Flysystem\FilesystemException $e) {
            throw new Exceptions\CreateDirectoryFailedException($this, $path, $e->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function deleteDirectory(string $path): void
    {
        try {
            $this->filesystem->deleteDirectory($path);
        } catch (\League\Flysystem\UnableToDeleteDirectory $e) {
            throw new Exceptions\DeleteFailedException($this, $path, $e->reason());
        } catch (\League\Flysystem\FilesystemException $e) {
            throw new Exceptions\DeleteFailedException($this, $path, $e->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function existsDirectory(string $path): bool
    {
        try {
            return $this->filesystem->directoryExists($path);
        } catch (\League\Flysystem\UnableToCheckExistence) {
            return false;
        }
    }
}
