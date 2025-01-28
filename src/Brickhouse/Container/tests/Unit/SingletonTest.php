<?php

use Brickhouse\Container\Container;

describe("Container::singleton", function () {
    it('returns same instance multiple times', function () {
        $container = new Container;
        $container->singleton('singleton', SingletonTestClass::class);

        $instance1 = $container->resolve('singleton');
        expect($instance1->value)->toBe('value');

        $instance2 = $container->resolve('singleton');
        expect($instance2->value)->toBe('value');

        $instance1->value = 'other value';

        expect($instance1->value)->toBe('other value');
        expect($instance2->value)->toBe('other value');
    });
});

class SingletonTestClass
{
    public string $value = 'value';
}
