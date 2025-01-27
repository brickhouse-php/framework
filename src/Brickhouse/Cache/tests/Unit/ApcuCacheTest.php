<?php

use Brickhouse\Cache\ApcuCache;
use Carbon\Carbon;

beforeEach(function () {})
    ->skip(function () {
        return !function_exists("apcu_enabled") || !\apcu_enabled();
    }, "APCu extension not available.");

describe('ApcuCache', function () {
    test('`get` returns null given non-existent key', function () {
        $cache = new ApcuCache;

        expect($cache->get('some-key'))->toBeNull();
    });

    test('`get` returns default value given non-existent key', function () {
        $cache = new ApcuCache;

        expect($cache->get('some-key', 1))->toBe(1);
    });

    test('`get` returns cached value given existent key', function () {
        $cache = new ApcuCache;

        $cache->set('some-key', 'some-value');

        expect($cache->get('some-key'))->toBe('some-value');
    });

    test('`get` returns null given expired key', function () {
        $cache = new ApcuCache;

        // Expires in 30 seconds
        Carbon::setTestNow('2000-01-01 00:00:00.000');
        $cache->set('some-key', 'some-value', 30);

        // Expires in 1 second
        Carbon::setTestNow('2000-01-01 00:00:29.000');
        expect($cache->get('some-key'))->toBe('some-value');

        // Expired
        Carbon::setTestNow('2000-01-01 00:00:30.000');
        expect($cache->get('some-key'))->toBeNull();
    });

    test('`has` returns false given non-existent key', function () {
        $cache = new ApcuCache;

        expect($cache->has('some-key'))->toBeFalse();
    });

    test('`has` returns true given existent key', function () {
        $cache = new ApcuCache;

        $cache->set('some-key', 'some-value');

        expect($cache->has('some-key'))->toBeTrue();
    });

    test('`has` returns false given expired key', function () {
        $cache = new ApcuCache;

        // Expires in 30 seconds
        Carbon::setTestNow('2000-01-01 00:00:00.000');
        $cache->set('some-key', 'some-value', 30);

        // Expires in 1 second
        Carbon::setTestNow('2000-01-01 00:00:29.000');
        expect($cache->has('some-key'))->toBeTrue();

        // Expired
        Carbon::setTestNow('2000-01-01 00:00:30.000');
        expect($cache->has('some-key'))->toBeFalse();
    });

    test('`getOrElse` returns generated value given non-existent key', function () {
        $cache = new ApcuCache;

        expect($cache->getOrElse('some-key', fn(string $key) => $key . '-1'))->toBe('some-key-1');
    });

    test('`getOrElse` returns generated value given expired key', function () {
        $cache = new ApcuCache;

        // Expires in 30 seconds
        Carbon::setTestNow('2000-01-01 00:00:00.000');
        $cache->set('some-key', 'some-value', 30);

        Carbon::setTestNow('2000-01-01 00:00:30.000');
        expect($cache->getOrElse('some-key', fn(string $key) => $key . '-1'))->toBe('some-key-1');
    });

    test('`getOrElse` returns cached value given existent key', function () {
        $cache = new ApcuCache;

        $cache->set('some-key', 'some-value');

        expect($cache->getOrElse('some-key', fn(string $key) => $key . '-1'))->toBe('some-value');
    });

    test('`delete` can handle non-existent key', function () {
        $cache = new ApcuCache;

        $cache->delete('some-key');
    })->throwsNoExceptions();

    test('`delete` deletes existent key', function () {
        $cache = new ApcuCache;

        $cache->set('some-key', 'some-value');
        expect($cache->get('some-key'))->toBe('some-value');

        $cache->delete('some-key');
        expect($cache->get('some-key'))->toBeNull();
    });

    test('`clear` can delete all keys', function () {
        $cache = new ApcuCache;

        $cache->set('some-key-1', 'some-value-1');
        $cache->set('some-key-2', 'some-value-2');
        $cache->set('some-key-3', 'some-value-3');
        $cache->set('some-key-4', 'some-value-4');

        $cache->clear();

        expect($cache->get('some-key-1'))->toBeNull();
        expect($cache->get('some-key-2'))->toBeNull();
        expect($cache->get('some-key-3'))->toBeNull();
        expect($cache->get('some-key-4'))->toBeNull();
    });
})->group('cache');
