<?php

use Brickhouse\Support\Arrayable;
use Brickhouse\Validation\Rules\Required;
use Brickhouse\Validation\Validator;

describe('Validator', function () {
    it('returns valid given empty rule set', function () {
        $validator = new Validator([]);

        $result = $validator->validate(['key' => 'some value']);

        expect($result->valid)->toBeTrue();
    });

    it('returns invalid given empty array', function () {
        $validator = new Validator([
            'key' => [new Required]
        ]);

        $result = $validator->validate([]);

        expect($result->valid)->toBeFalse();
        expect($result->invalid)->toBeTrue();
        expect($result->errors)->toMatchArray([
            'key' => ['Field {attribute} is required.']
        ]);
    });

    it('validates nested arrays (invalid)', function () {
        $validator = new Validator([
            'nested.value' => [new Required]
        ]);

        $result = $validator->validate([
            'nested' => [
                'value' => null,
            ]
        ]);

        expect($result->valid)->toBeFalse();
        expect($result->invalid)->toBeTrue();
        expect($result->errors)->toMatchArray([
            'nested.value' => ['Field {attribute} is required.']
        ]);
    });

    it('validates nested arrays (valid)', function () {
        $validator = new Validator([
            'nested.value' => [new Required]
        ]);

        $result = $validator->validate([
            'nested' => [
                'value' => 'valid value',
            ]
        ]);

        expect($result->valid)->toBeTrue();
        expect($result->invalid)->toBeFalse();
    })->group('t');

    it('validates Arrayable', function () {
        $validator = new Validator([
            'value' => [new Required]
        ]);

        $result = $validator->validate(new class implements Arrayable
        {
            public function toArray(): array
            {
                return ['value' => 'some-value'];
            }
        });

        expect($result->valid)->toBeTrue();
        expect($result->invalid)->toBeFalse();
    });

    it('validates object instance', function () {
        $validator = new Validator([
            'value' => [new Required]
        ]);

        $result = $validator->validate(new class
        {
            public string $value = 'some value';
        });

        expect($result->valid)->toBeTrue();
        expect($result->invalid)->toBeFalse();
    });

    it('validates indexed arrays', function () {
        $validator = new Validator([
            0 => [new Required]
        ]);

        $result = $validator->validate(['some value']);

        expect($result->valid)->toBeTrue();
        expect($result->invalid)->toBeFalse();
    });

    it('uses custom message if given', function () {
        $validator = new Validator([
            'key' => [new Required('Field must be given.')]
        ]);

        $result = $validator->validate([]);

        expect($result->valid)->toBeFalse();
        expect($result->invalid)->toBeTrue();
        expect($result->errors)->toMatchArray([
            'key' => ['Field must be given.']
        ]);
    });

    it('skips rule if condition is not met (if)', function () {
        $validator = new Validator([
            'key' => [
                new Required(if: fn(array $data) => !isset($data['other-key']))
            ],
        ]);

        $result = $validator->validate([
            'other-key' => 1
        ]);

        expect($result->valid)->toBeTrue();
        expect($result->invalid)->toBeFalse();
    });

    it('skips rule if condition is not met (unless)', function () {
        $validator = new Validator([
            'key' => [
                new Required(unless: fn(array $data) => isset($data['other-key']))
            ],
        ]);

        $result = $validator->validate([
            'other-key' => 1
        ]);

        expect($result->valid)->toBeTrue();
        expect($result->invalid)->toBeFalse();
    });

    it('does not skip rule if condition is met (if)', function () {
        $validator = new Validator([
            'key' => [
                new Required(if: fn(array $data) => !isset($data['other-key']))
            ],
        ]);

        $result = $validator->validate([]);

        expect($result->valid)->toBeFalse();
        expect($result->invalid)->toBeTrue();
        expect($result->errors)->toMatchArray([
            'key' => [
                'Field {attribute} is required.'
            ]
        ]);
    });

    it('does not skip rule if condition is not met (unless)', function () {
        $validator = new Validator([
            'key' => [
                new Required(unless: fn(array $data) => isset($data['other-key']))
            ],
        ]);

        $result = $validator->validate([]);

        expect($result->valid)->toBeFalse();
        expect($result->invalid)->toBeTrue();
        expect($result->errors)->toMatchArray([
            'key' => [
                'Field {attribute} is required.'
            ]
        ]);
    });
})->group('validation', 'validator');
