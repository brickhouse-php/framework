<?php

use Brickhouse\Container\Container;

describe("Container::call", function () {
    it('calls closure without parameters', function () {
        $container = new Container;

        $value = $container->call(null, fn() => 'return value');

        expect($value)->toBe('return value');
    });

    it('calls closure with injected parameters', function () {
        $container = new Container;

        $container->bind(\SimpleClassInstance::class);

        $value = $container->call(null, fn(\SimpleClassInstance $simple) => $simple->value);

        expect($value)->toBe(5);
    });

    it('calls closure with new `$this` instance', function () {
        $container = new Container;
        $instance = new \SimpleClassInstance;

        $value = $container->call($instance, fn() => $this->value);

        expect($value)->toBe(5);
    });
});
