<?php

use Brickhouse\Container\Container;
use Brickhouse\Container\Exceptions\ContainerEntryMissingException;

describe("Container::get", function () {
    it('throws ContainerEntryMissingException for an empty container')
        ->expect(fn() => new Container()->get('any key'))
        ->throws(ContainerEntryMissingException::class);

    it('throws ContainerEntryMissingException given missing key', function () {
        $container = new Container;
        $container->instance('some-key', 'some-value');

        $container->get('anything');
    })->throws(ContainerEntryMissingException::class);

    it('returns value registered with existing key', function () {
        $container = new Container;
        $container->instance('some-key', 'some-value');

        $value = $container->get('some-key');

        expect($value)->toBe('some-value');
    });

    it('returns value from closure', function () {
        $container = new Container;
        $container->bind('some-key', fn() => 'some-value');

        $value = $container->get('some-key');

        expect($value)->toBe('some-value');
    });

    it('returns aliased value', function () {
        $container = new Container;
        $container->instance('some-key', 'some-value');
        $container->alias('other-key', 'some-key');

        $value = $container->get('other-key');

        expect($value)->toBe('some-value');
    });

    it('returns resolved class', function () {
        $container = new Container;
        $container->bind(ResolvedClassInstance::class);

        $value = $container->get(ResolvedClassInstance::class);

        expect($value)->toBeInstanceOf(ResolvedClassInstance::class);
        expect($value->inner)->toBeInstanceOf(SimpleClassInstance::class);
        expect($value->inner->value)->toBe(5);
    });

    it('returns latest added value', function () {
        $container = new Container;
        $container->bind('key', fn() => 'first added');
        $container->bind('key', fn() => 'second added');

        $value = $container->get('key');

        expect($value)->toBe('second added');
    });
});

describe("Container::getAll", function () {
    it('throws ContainerEntryMissingException for an empty container')
        ->expect(fn() => new Container()->getAll('any key'))
        ->throws(ContainerEntryMissingException::class);

    it('throws ContainerEntryMissingException given missing key', function () {
        $container = new Container;
        $container->instance('some-key', 'some-value');

        $container->getAll('anything');
    })->throws(ContainerEntryMissingException::class);

    it('returns array with concrete types', function () {
        $container = new Container;
        $container->instance('some-key', 'value1');
        $container->instance('some-key', 'value2');
        $container->instance('some-key', 'value3');

        $values = $container->getAll('some-key');

        expect($values)->toMatchArray(['value1', 'value2', 'value3']);
    });

    it('returns array with closure types', function () {
        $container = new Container;
        $container->bind('some-key', fn() => 'value1');
        $container->bind('some-key', fn() => 'value2');
        $container->bind('some-key', fn() => 'value3');

        $values = $container->getAll('some-key');

        expect($values)->toMatchArray(['value1', 'value2', 'value3']);
    });
});

class SimpleClassInstance
{
    public int $value = 5;
}

class ResolvedClassInstance
{
    public function __construct(
        public readonly SimpleClassInstance $inner,
    ) {}
}
