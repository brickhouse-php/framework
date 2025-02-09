<?php

use Brickhouse\Validation\Rules\Acceptance;

describe('Acceptance', function () {
    it('returns error given falsy string', function (mixed $value) {
        $result = new Acceptance()->validate('key', $value);

        expect($result)->not->toBeNull();
        expect($result)->toBe('Field {attribute} must be set.');
    })->with([
        null,
        '',
        '0',
        'false',
        0,
        false,
        '[]',
        'reject',
        'accept'
    ]);

    it('returns null given truthy value', function (mixed $value) {
        $result = new Acceptance()->validate('key', $value);

        expect($result)->toBeNull();
    })->with([
        'true',
        true,
        1,
        '1',
        'TRUE',
        'yes',
        'YES',
        'on',
        'ON'
    ]);

    it('returns null given value within custom matches', function () {
        $result = new Acceptance(matches: ['accept'])->validate('key', 'accept');

        expect($result)->toBeNull();
    });
})->group('validation', 'rule');
