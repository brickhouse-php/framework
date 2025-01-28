<?php

use Brickhouse\Container\Container;
use Brickhouse\Container\Exceptions\ResolutionFailedException;

describe("Container::build", function () {
    it('throws ResolutionFailedException given invalid class name', function () {
        new Container()->build('InvalidClassName');
    })->throws(ResolutionFailedException::class);

    it('throws ResolutionFailedException given un-instantiable class', function () {
        new Container()->build(AbstractClass::class);
    })->throws(ResolutionFailedException::class);

    it('builds closure', function () {
        $container = new Container;

        $value = $container->build(fn() => 'return value');

        expect($value)->toBe('return value');
    });

    it('builds class with named parameters', function () {
        $container = new Container;

        $value = $container->build(ClassWithScalarInject::class, [
            'integer' => 42,
            'string' => 'string',
            'bool' => true
        ]);

        expect($value)->toBeInstanceOf(ClassWithScalarInject::class);
        expect($value->integer)->toBe(42);
        expect($value->string)->toBe('string');
        expect($value->bool)->toBeTrue();
    });

    it('builds class with unnamed parameters', function () {
        $container = new Container;

        $value = $container->build(ClassWithScalarInject::class, [
            42,
            'string',
            true
        ]);

        expect($value)->toBeInstanceOf(ClassWithScalarInject::class);
        expect($value->integer)->toBe(42);
        expect($value->string)->toBe('string');
        expect($value->bool)->toBeTrue();
    });

    it('builds class with unnamed class parameter', function () {
        $container = new Container;

        $value = $container->build(ClassWithClassInject::class, [
            new ClassWithScalarInject(42, 'string', true)
        ]);

        expect($value)->toBeInstanceOf(ClassWithClassInject::class);
        expect($value->inner->integer)->toBe(42);
        expect($value->inner->string)->toBe('string');
        expect($value->inner->bool)->toBeTrue();
    });

    it('builds class with default parameters', function () {
        $container = new Container;

        $value = $container->build(ClassWithDefaultValue::class);

        expect($value)->toBeInstanceOf(ClassWithDefaultValue::class);
        expect($value->integer)->toBe(14);
    });

    it('builds class with variadic parameters', function () {
        $container = new Container;

        $value = $container->build(ClassWithVariadicValue::class);

        expect($value)->toBeInstanceOf(ClassWithVariadicValue::class);
        expect($value->givenIntegers)->toBeEmpty();
    });

    it('builds class with nullable parameter', function () {
        $container = new Container;

        $value = $container->build(ClassWithNullableParameter::class);

        expect($value)->toBeInstanceOf(ClassWithNullableParameter::class);
        expect($value->nullable)->toBeNull();
    });

    it('throws ResolutionFailedException given class with primitive parameter', function () {
        new Container()->build(ClassWithScalarInject::class);
    })->throws(ResolutionFailedException::class);
});

abstract class AbstractClass
{
    public abstract function __construct();
}

class ClassWithScalarInject
{
    public function __construct(
        public readonly int $integer,
        public readonly string $string,
        public readonly bool $bool,
    ) {}
}

class ClassWithClassInject
{
    public function __construct(
        public readonly ClassWithScalarInject $inner,
    ) {}
}

class ClassWithDefaultValue
{
    public function __construct(
        public readonly int $integer = 14,
    ) {}
}

class ClassWithVariadicValue
{
    public readonly array $givenIntegers;

    public function __construct(int ...$ints)
    {
        $this->givenIntegers = $ints;
    }
}

class ClassWithNullableParameter
{
    public function __construct(public readonly null|string $nullable) {}
}
