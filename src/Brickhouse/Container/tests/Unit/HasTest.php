<?php

use Brickhouse\Container\Container;

describe("Container::has", function () {
    it('returns false for an empty container')
        ->expect(fn() => new Container()->has('any key'))
        ->toBeFalse();

    it('returns false given missing key', function () {
        $container = new Container;
        $container->instance('some-key', 'some-value');

        $result = $container->has('anything');

        expect($result)->toBeFalse();
    });

    it('returns true given existing key', function () {
        $container = new Container;
        $container->instance('some-key', 'some-value');

        $result = $container->has('some-key');

        expect($result)->toBeTrue();
    });

    it('returns true given closure key', function () {
        $container = new Container;
        $container->bind('some-key', fn() => 'some-value');

        $result = $container->has('some-key');

        expect($result)->toBeTrue();
    });
});
