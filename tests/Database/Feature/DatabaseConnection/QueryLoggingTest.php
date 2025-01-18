<?php

use Brickhouse\Database\Sqlite\SqliteConnection;

describe('Query logging', function () {
    it('returns empty logs when logging disabled', function () {
        /** @var SqliteConnection $database */
        $database = $this->inMemoryDatabase();

        $database->select("SELECT 1");

        expect($database->getQueryLog())->toBeEmpty();
    });

    it('returns single log given single query', function () {
        /** @var SqliteConnection $database */
        $database = $this->inMemoryDatabase();
        $database->enableQueryLogging();

        $database->select("SELECT 1");

        $log = $database->getQueryLog();

        expect($log)->toBeArray()->toHaveCount(1);
        expect($log[0])->toBeArray();
        expect($log[0]['query'])->toBe("SELECT 1");
        expect($log[0]['bindings'])->toBeEmpty();
    });

    it('returns logs given logging period', function () {
        /** @var SqliteConnection $database */
        $database = $this->inMemoryDatabase();

        $database->enableQueryLogging();
        $database->select("SELECT 1");
        $database->select("SELECT 2");
        $database->select("SELECT 3");
        $database->disableQueryLogging();
        $database->select("SELECT 4");

        $log = $database->getQueryLog();

        expect($log)->toBeArray()->toHaveCount(3);
        expect($log[0])->toMatchArray([
            'query' => 'SELECT 1',
            'bindings' => [],
        ]);
        expect($log[1])->toMatchArray([
            'query' => 'SELECT 2',
            'bindings' => [],
        ]);
        expect($log[2])->toMatchArray([
            'query' => 'SELECT 3',
            'bindings' => [],
        ]);
    });
})->group('database');
