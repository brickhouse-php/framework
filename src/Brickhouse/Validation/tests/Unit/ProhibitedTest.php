<?php

use Brickhouse\Validation\Rules\Prohibited;

describe('Prohibited', function () {
    it('returns null given null value', function () {
        $result = new Prohibited()->validate('key', null);

        expect($result)->toBeNull();
    });

    it('returns null given empty array', function () {
        $result = new Prohibited()->validate('key', []);

        expect($result)->toBeNull();
    });

    it('returns null given empty string', function () {
        $result = new Prohibited()->validate('key', '');

        expect($result)->toBeNull();
    });

    it('returns null given non-empty string', function () {
        $result = new Prohibited()->validate('key', 'value');

        expect($result)->not->toBeNull();
        expect($result)->toBe('Field {attribute} is prohibited.');
    });

    it('returns null given non-empty array', function () {
        $result = new Prohibited()->validate('key', ['value']);

        expect($result)->not->toBeNull();
        expect($result)->toBe('Field {attribute} is prohibited.');
    });

    it('returns null given object', function () {
        $result = new Prohibited()->validate('key', (object)['some value']);

        expect($result)->not->toBeNull();
        expect($result)->toBe('Field {attribute} is prohibited.');
    });
})->group('validation', 'rule');
