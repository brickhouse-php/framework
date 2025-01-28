<?php

use Brickhouse\Container\Container;

describe("Container::alias", function () {
    it('throws InvalidArgumentException given same concrete and abstract type', function () {
        $container = new Container;
        $container->alias('type', 'type');
    })->throws(\InvalidArgumentException::class);

    it('can get alias type', function () {
        $container = new Container;
        $container->alias('type', 'other-type');

        $result = $container->getAlias('type');

        expect($result)->toBe('other-type');
    });

    it('is aliased', function () {
        $container = new Container;
        $container->alias('type', 'other-type');

        $result = $container->isAlias('type');

        expect($result)->toBeTrue();
    });

    it('is not aliased given no alias', function () {
        $container = new Container;

        $result = $container->isAlias('type');

        expect($result)->toBeFalse();
    });
});
