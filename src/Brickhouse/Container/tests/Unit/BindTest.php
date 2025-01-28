<?php

use Brickhouse\Container\Container;

describe("Container::bind", function () {
    it('binds value to be retrieved', function () {
        $container = new Container;
        $container->bind('some-key', fn() => 'some-value');

        $value = $container->get('some-key');

        expect($value)->toBe('some-value');
    });
});

describe("Container::bindIf", function () {
    it('binds value given no existing bind', function () {
        $container = new Container;
        $container->bindIf('some-key', fn() => 'some-value');

        $value = $container->get('some-key');

        expect($value)->toBe('some-value');
    });

    it('skips bind given existing bind', function () {
        $container = new Container;
        $container->bindIf('some-key', fn() => 'some-value');
        $container->bindIf('some-key', fn() => 'some-other-value');

        $value = $container->get('some-key');

        expect($value)->toBe('some-value');
    });
});

describe("Container::scopedIf", function () {
    it('binds value given no existing bind', function () {
        $container = new Container;
        $container->scopedIf('some-key', fn() => 'some-value');

        $value = $container->get('some-key');

        expect($value)->toBe('some-value');
    });

    it('skips bind given existing bind', function () {
        $container = new Container;
        $container->scopedIf('some-key', fn() => 'some-value');
        $container->scopedIf('some-key', fn() => 'some-other-value');

        $value = $container->get('some-key');

        expect($value)->toBe('some-value');
    });
});

describe("Container::singletonIf", function () {
    it('binds value given no existing bind', function () {
        $container = new Container;
        $container->singletonIf('some-key', fn() => 'some-value');

        $value = $container->get('some-key');

        expect($value)->toBe('some-value');
    });

    it('skips bind given existing bind', function () {
        $container = new Container;
        $container->singletonIf('some-key', fn() => 'some-value');
        $container->singletonIf('some-key', fn() => 'some-other-value');

        $value = $container->get('some-key');

        expect($value)->toBe('some-value');
    });
});

describe("Container::instanceIf", function () {
    it('binds value given no existing bind', function () {
        $container = new Container;
        $container->instanceIf('some-key', 'some-value');

        $value = $container->get('some-key');

        expect($value)->toBe('some-value');
    });

    it('skips bind given existing bind', function () {
        $container = new Container;
        $container->instanceIf('some-key', 'some-value');
        $container->instanceIf('some-key', 'some-other-value');

        $value = $container->get('some-key');

        expect($value)->toBe('some-value');
    });
});
