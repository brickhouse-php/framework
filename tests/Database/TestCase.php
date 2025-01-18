<?php

namespace Brickhouse\Tests\Database;

use Brickhouse\Core\Application;
use Brickhouse\Database\DatabaseConnection;
use Brickhouse\Database\Sqlite\SqliteConnection;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    public static function setUpBeforeClass(): void
    {
        Application::configure()->create();
    }

    /**
     * Create an in-memory SQLite database connection.
     *
     * @return DatabaseConnection
     */
    protected function inMemoryDatabase(): DatabaseConnection
    {
        return SqliteConnection::inMemory();
    }
}
