<?php

use Brickhouse\Support\Collection;

describe('Collections::wrap()', function () {
    it('creates empty collection given empty array', function () {
        $array = [];

        $result = Collection::wrap($array);

        expect($result->items())->toBeEmpty();
    });

    it('creates collection with single item given scalar', function () {
        $item = "value";

        $result = Collection::wrap($item);

        expect($result->items())->toEqual(["value"]);
    });

    it('creates wrapped collection given collection', function () {
        $item = Collection::make(["value1", "value2"]);

        $result = Collection::wrap($item);

        expect($result->items())->toEqual(["value1", "value2"]);
    });

    it('preserves keys in wrapped arrays', function () {
        $array = ["key" => "value"];

        $result = Collection::wrap($array);

        expect($result->items())->toEqual(["key" => "value"]);
    });
})->group('support');
