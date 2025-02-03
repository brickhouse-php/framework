<?php

describe('array_wrap', function () {
    it('returns empty array given empty array', function () {
        $item = [];

        $result = array_wrap($item);

        expect($result)->toBeArray()->toBeEmpty();
    });

    it('returns array given scalar', function () {
        $item = "value";

        $result = array_wrap($item);

        expect($result)->toBeArray()->toEqual(["value"]);
    });

    it('returns array with keys given keyed array', function () {
        $item = ["key" => "value"];

        $result = array_wrap($item);

        expect($result)->toBeArray()->toEqual(["key" => "value"]);
    });
})->group('support');
