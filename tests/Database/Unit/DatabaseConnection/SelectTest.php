<?php

use Brickhouse\Database\Sqlite\SqliteConnection;

describe('DatabaseConnection::select()', function () {
    it('returns array of returned rows', function () {
        /** @var SqliteConnection $database */
        $database = $this->inMemoryDatabase();

        $rows = $database->select("SELECT * FROM (VALUES(1), (2))");

        expect($rows)->toEqual([
            ["column1" => 1],
            ["column1" => 2]
        ]);
    });
})->group('database');
