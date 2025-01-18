<?php

describe('array_flatten', function () {
    it('returns empty array given empty array', function () {
        $array = [];

        $result = array_flatten($array);

        expect($result)->toBeEmpty();
    });

    it('returns flat array given flat array', function () {
        $array = [1, 2, 3];

        $result = array_flatten($array);

        expect($result)->toEqual([1, 2, 3]);
    });

    it('returns flat array given nested array', function () {
        $array = [[1, 2], [3, 4], [5, 6]];

        $result = array_flatten($array);

        expect($result)->toEqual([1, 2, 3, 4, 5, 6]);
    });

    it('returns flat array given deeply nested array', function () {
        $array = [[[1], [2]], [[3], [4]], [[5], [6]]];

        $result = array_flatten($array);

        expect($result)->toEqual([1, 2, 3, 4, 5, 6]);
    });

    it('returns array without array keys', function () {
        $array = ["key1" => "value1", "key2" => "value2"];

        $result = array_flatten($array);

        expect($result)->toEqual(["value1", "value2"]);
    });

    it('returns single-level flat array given nested array', function () {
        $array = [[[1], [2]], [[3], [4]], [[5], [6]]];

        $result = array_flatten($array, 1);

        expect($result)->toEqual([[1], [2], [3], [4], [5], [6]]);
    });
})->group('support');
