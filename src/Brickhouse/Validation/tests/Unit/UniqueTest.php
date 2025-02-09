<?php

use Brickhouse\Core\Application;
use Brickhouse\Database\ConnectionManager;
use Brickhouse\Database\DatabaseConfig;
use Brickhouse\Database\Schema\Blueprint;
use Brickhouse\Database\Schema\Schema;
use Brickhouse\Database\Sqlite\SqliteConnectionString;
use Brickhouse\Database\Transposer\DatabaseModel;
use Brickhouse\Database\Transposer\Model;
use Brickhouse\Validation\Rules\Unique;
use Brickhouse\Validation\Validator;

beforeEach(function () {
    $manager = new ConnectionManager(new DatabaseConfig([
        SqliteConnectionString::inMemory(),
    ]));

    Application::current()->instance($manager::class, $manager);

    $schema = new Schema();

    $schema->create('users', function (Blueprint $table) {
        $table->id();
        $table->text('name');
        $table->text('email');
    });

    $schema->create('teams', function (Blueprint $table) {
        $table->id();
        $table->text('name');
        $table->text('organization');
    });
});

afterEach(function () {
    $schema = new Schema();

    $schema->drop('users');
    $schema->drop('teams');
});

describe('Unique', function () {
    it('returns valid given empty table', function () {
        $validator = new Validator([
            'email' => [
                new Unique()
            ]
        ]);

        $result = $validator->validate(
            new User('Max T. Kristiansen', 'me@maxtrier.dk')
        );

        expect($result->valid)->toBeTrue();
        expect($result->invalid)->toBeFalse();
    });

    it('returns valid given model with different column value', function () {
        User::create(['name' => 'Steve Jobs', 'email' => 'steve@apple.com']);

        $validator = new Validator([
            'email' => [
                new Unique()
            ]
        ]);

        $result = $validator->validate(
            new User('Max T. Kristiansen', 'me@maxtrier.dk')
        );

        expect($result->valid)->toBeTrue();
        expect($result->invalid)->toBeFalse();
    });

    it('returns invalid given model with same column value', function () {
        User::create(['name' => 'Evil Max', 'email' => 'me@maxtrier.dk']);

        $validator = new Validator([
            'email' => [
                new Unique()
            ]
        ]);

        $result = $validator->validate(
            new User('Max T. Kristiansen', 'me@maxtrier.dk')
        );

        expect($result->valid)->toBeFalse();
        expect($result->invalid)->toBeTrue();
    });

    it('returns invalid given model with same column value inside scope', function () {
        Team::create(['name' => 'Marketing', 'organization' => 'Example Ltd.']);

        $validator = new Validator([
            'name' => [
                new Unique(scope: ['organization'])
            ]
        ]);

        $result = $validator->validate(
            new Team('Marketing', 'Example Ltd.')
        );

        expect($result->valid)->toBeFalse();
        expect($result->invalid)->toBeTrue();
    });

    it('returns valid given model with same column value outside scope', function () {
        Team::create(['name' => 'Marketing', 'organization' => 'Example Corp.']);

        $validator = new Validator([
            'name' => [
                new Unique(scope: ['organization'])
            ]
        ]);

        $result = $validator->validate(
            new Team('Marketing', 'Example Ltd.')
        );

        expect($result->valid)->toBeTrue();
        expect($result->invalid)->toBeFalse();
    });
})->group('validation', 'rule');

class User implements Model
{
    use DatabaseModel;

    public function __construct(
        public string $name,
        public string $email,
    ) {}
}

class Team implements Model
{
    use DatabaseModel;

    public function __construct(
        public string $name,
        public string $organization,
    ) {}
}
