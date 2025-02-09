<?php

use Brickhouse\Validation\Rules\Required;

describe('Required', function () {
    it('returns error given null value', function () {
        $result = new Required()->validate('key', null);

        expect($result)->not->toBeNull();
        expect($result)->toBe('Field {attribute} is required.');
    });

    it('returns error given empty array', function () {
        $result = new Required()->validate('key', []);

        expect($result)->not->toBeNull();
        expect($result)->toBe('Field {attribute} is required.');
    });

    it('returns error given empty string', function () {
        $result = new Required()->validate('key', '');

        expect($result)->not->toBeNull();
        expect($result)->toBe('Field {attribute} is required.');
    });

    it('returns null given non-empty string', function () {
        $result = new Required()->validate('key', 'value');

        expect($result)->toBeNull();
    });

    it('returns null given non-empty array', function () {
        $result = new Required()->validate('key', ['value']);

        expect($result)->toBeNull();
    });

    it('returns null given object', function () {
        $result = new Required()->validate('key', (object)['some value']);

        expect($result)->toBeNull();
    });
})->group('validation', 'rule');
