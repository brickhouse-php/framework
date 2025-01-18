<?php

namespace Brickhouse\Filesystem\Adapters;

class LocalFilesystemBuilder
{
    /**
     * Defines whether to acquire an exclusive lock whenever a file is being written to.
     *
     * @var boolean
     */
    protected bool $exclusiveLock = true;

    /**
     * Defines the UNIX file permissions for files in the file system.
     *
     * @var int
     */
    protected int $filePermissions = 0640;

    /**
     * Defines the UNIX file permissions for directories in the file system.
     *
     * @var int
     */
    protected int $directoryPermissions = 0740;

    public function __construct(
        public string $path
    ) {}

    /**
     * Whenever a file is being written to, do so after acquiring an exclusive lock on the file. By default, the file is locked.
     *
     * @return self
     */
    public function withLocking(): self
    {
        $this->exclusiveLock = true;
        return $this;
    }

    /**
     * Whenever a file is being written to, do so without acquiring an exclusive lock.
     *
     * @return self
     */
    public function withoutLocking(): self
    {
        $this->exclusiveLock = false;
        return $this;
    }

    /**
     * Defines the default permissions for all new files and folders within the file system.
     *
     * @param int   $permissions    UNIX-style permissions.
     *
     * @return self
     */
    public function withPermissions(int $permissions): self
    {
        $this->withFilePermissions($permissions);
        $this->withDirectoryPermissions($permissions);

        return $this;
    }

    /**
     * Defines the default permissions for all new files within the file system.
     *
     * @param int   $permissions    UNIX-style permissions.
     *
     * @return self
     */
    public function withFilePermissions(int $permissions): self
    {
        $this->filePermissions = $permissions;

        return $this;
    }

    /**
     * Defines the default permissions for all new directories within the file system.
     *
     * @param int   $permissions    UNIX-style permissions.
     *
     * @return self
     */
    public function withDirectoryPermissions(int $permissions): self
    {
        $this->directoryPermissions = $permissions;

        return $this;
    }

    /**
     * Builds the local file system into a new `LocalFilesystem`-instance.
     *
     * @return LocalFilesystem
     */
    public function build(): LocalFilesystem
    {
        return new LocalFilesystem(
            $this->path,
            $this->exclusiveLock,
            $this->filePermissions,
            $this->directoryPermissions
        );
    }
}
