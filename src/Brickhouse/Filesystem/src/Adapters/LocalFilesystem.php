<?php

namespace Brickhouse\Filesystem\Adapters;

use Brickhouse\Filesystem\Exceptions;
use Brickhouse\Filesystem\Filesystem;

class LocalFilesystem implements Filesystem
{
    protected int $fileFlags {
        get => $this->exclusiveLock ? LOCK_EX : 0;
    }

    /**
     * Creates a new local filesystem adapter.
     *
     * @param string    $root                   Defines the root location of the file system.
     * @param bool      $exclusiveLock          Defines whether to acquire exclusive locks whenever files are written.
     * @param int       $filePermissions        Defines the UNIX file permissions for files in the file system.
     * @param int       $directoryPermissions   Defines the UNIX file permissions for directories in the file system.
     */
    public function __construct(
        public readonly string $root,
        protected readonly bool $exclusiveLock = true,
        protected readonly int $filePermissions = 0640,
        protected readonly int $directoryPermissions = 0740,
    ) {}

    /**
     * @inheritDoc
     */
    public function read(string $path): string
    {
        $location = path($this->root, $path);
        $content = @file_get_contents($location);

        if ($content === false) {
            throw new Exceptions\ReadFailedException($this, $location, error_get_last()['message'] ?? '');
        }

        return $content;
    }

    /**
     * @inheritDoc
     */
    public function write(string $path, string $contents): void
    {
        $path = path($this->root, $path);

        $this->createDirectory(dirname($path));

        if (@file_put_contents($path, $contents, $this->fileFlags) === false) {
            throw new Exceptions\WriteFailedException($this, $path, error_get_last()['message'] ?? '');
        }

        if (@chmod($path, $this->filePermissions) === false) {
            throw new Exceptions\PermissionChangeFailedException($this, $path, error_get_last()['message'] ?? '');
        }
    }

    /**
     * @inheritDoc
     */
    public function append(string $path, string $contents): void
    {
        $path = path($this->root, $path);

        if (@file_put_contents($path, $contents, $this->fileFlags | FILE_APPEND) === false) {
            throw new Exceptions\WriteFailedException($this, $path, error_get_last()['message'] ?? '');
        }
    }

    /**
     * @inheritDoc
     */
    public function delete(string $path): void
    {
        $path = path($this->root, $path);

        if (@unlink($path) === false) {
            throw new Exceptions\DeleteFailedException($this, $path, error_get_last()['message'] ?? '');
        }
    }

    /**
     * @inheritDoc
     */
    public function exists(string $path): bool
    {
        $path = path($this->root, $path);

        clearstatcache(true, $path);

        return file_exists($path) && is_file($path);
    }

    /**
     * @inheritDoc
     */
    public function createDirectory(string $path): void
    {
        $path = path($this->root, $path);

        if (is_dir($path)) {
            return;
        }

        if (!@mkdir($path, $this->directoryPermissions, recursive: true)) {
            throw new Exceptions\CreateDirectoryFailedException($this, $path, error_get_last()['message'] ?? '');
        }
    }

    /**
     * @inheritDoc
     */
    public function deleteDirectory(string $path): void
    {
        $path = path($this->root, $path);

        if (@rmdir($path) === false) {
            throw new Exceptions\DeleteFailedException($this, $path, error_get_last()['message'] ?? '');
        }
    }

    /**
     * @inheritDoc
     */
    public function existsDirectory(string $path): bool
    {
        $path = path($this->root, $path);

        clearstatcache(true, $path);

        return file_exists($path) && is_dir($path);
    }
}
