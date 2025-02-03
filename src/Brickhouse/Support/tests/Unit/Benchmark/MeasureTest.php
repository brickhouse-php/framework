<?php

use Brickhouse\Support\Benchmark;

describe('benchmark', function () {
    it('measure returns the result and the duration', function () {
        $result = Benchmark::measure(fn() => 1 + 1);

        expect($result)->toBeArray()->toHaveCount(2);
        expect($result[0])->toBeInt()->toEqual(2);
        expect($result[1])->toBeFloat()->toBeGreaterThan(0.0);
    });
})->group('support');
