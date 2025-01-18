<?php

namespace Brickhouse\Filesystem;

use Brickhouse\Filesystem\Exceptions;

interface Filesystem
{
    /**
     * Reads the file at the specified path and returns it's content.
     *
     * @param string    $path   Path to the file to read.
     *
     * @return string
     *
     * @throws Exceptions\ReadFailedException       Thrown if the file could not be read.
     */
    public function read(string $path): string;

    /**
     * Writes the given content to the file at the specified path.
     *
     * If the file doesn't exist, it is created. If the file already exists, it is overwritten.
     *
     * @param string    $path       Path to the file to write to.
     * @param string    $contents   The content to write to the file.
     *
     * @return void
     *
     * @throws Exceptions\WriteFailedException      Thrown if the file could not be written to.
     */
    public function write(string $path, string $contents): void;

    /**
     * Appends the given content to the file at the specified path. If the file doesn't exist, it is created.
     *
     * @param string    $path       Path to the file to append to.
     * @param string    $contents   The content to append to the file.
     *
     * @return void
     *
     * @throws Exceptions\WriteFailedException      Thrown if the file could not be written to.
     */
    public function append(string $path, string $contents): void;

    /**
     * Deletes the file at the specified path.
     *
     * @param string    $path       Path to the file to delete.
     *
     * @return void
     *
     * @throws Exceptions\DeleteFailedException     Thrown if the file could not be deleted.
     */
    public function delete(string $path): void;

    /**
     * Checks whether the file at the specified exists.
     *
     * @param string    $path       Path to the file.
     *
     * @return bool
     */
    public function exists(string $path): bool;

    /**
     * Creates a new directory at the specified path. If the directory already exists, nothing is done.
     *
     * @param string    $path       Path to the directory to create.
     *
     * @return void
     */
    public function createDirectory(string $path): void;

    /**
     * Deletes the directory at the specified path.
     *
     * @param string    $path       Path to the directory to delete.
     *
     * @return void
     */
    public function deleteDirectory(string $path): void;

    /**
     * Checks whether the directory at the specified exists.
     *
     * @param string    $path       Path to the directory.
     *
     * @return bool
     */
    public function existsDirectory(string $path): bool;
}
