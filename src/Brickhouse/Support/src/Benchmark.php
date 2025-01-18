<?php

namespace Brickhouse\Support;

/**
 * @template TKey of array-key
 * @template TValue
 */
class Benchmark
{
    /**
     * Measure a callable or array of callable and return their execution times.
     *
     * @template TResult
     *
     * @param \Closure(): TResult $callback
     *
     * @return array{0:TResult,1:float}
     */
    public static function measure(\Closure $callback): array
    {
        gc_collect_cycles();

        $start = hrtime(true);

        $result = $callback();

        return [$result, (hrtime(true) - $start) / 1_000_000];
    }
}
