<?php

describe('array_pull', function () {
    it('returns null given empty array', function () {
        $array = [];

        $result = array_pull($array, "key");

        expect($result)->toBeNull();
    });

    it('returns default value given empty array', function () {
        $array = [];

        $result = array_pull($array, "key", default: 1);

        expect($result)->toEqual(1);
    });

    it('returns default value given missing key', function () {
        $array = ["key" => "value"];

        $result = array_pull($array, "other-key", default: 1);

        expect($result)->toEqual(1);
    });

    it('returns value given present key', function () {
        $array = ["key" => "value"];

        $result = array_pull($array, "key");

        expect($result)->toEqual("value");
    });

    it('removes key from array given present key', function () {
        $array = ["key" => "value"];

        array_pull($array, "key");

        expect($array)->toBeEmpty();
    });

    it('calls default if callable', function () {
        $array = ["key" => "value"];

        $result = array_pull($array, "other-key", default: fn() => "default value");

        expect($result)->toEqual("default value");
    });
})->group('support');
