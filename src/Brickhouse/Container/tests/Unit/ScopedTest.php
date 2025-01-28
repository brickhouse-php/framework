<?php

use Brickhouse\Container\Container;

describe("Container::scoped", function () {
    it('returns same instance multiple times', function () {
        $container = new Container;
        $container->scoped('scoped', ScopedTestClass::class);

        $instance1 = $container->resolve('scoped');
        expect($instance1->value)->toBe('value');

        $instance2 = $container->resolve('scoped');
        expect($instance2->value)->toBe('value');

        $instance1->value = 'other value';

        expect($instance1->value)->toBe('other value');
        expect($instance2->value)->toBe('other value');
    });

    it('returns new instance after scope end', function () {
        $container = new Container;
        $container->scoped('scoped', ScopedTestClass::class);

        $instance1 = $container->resolve('scoped');
        expect($instance1->value)->toBe('value');

        $instance1->value = 'other value';

        $instance2 = $container->resolve('scoped');
        expect($instance2->value)->toBe('other value');

        $container->terminateScope();

        $instance = $container->resolve('scoped');
        expect($instance->value)->toBe('value');
    });
});

class ScopedTestClass
{
    public string $value = 'value';
}
