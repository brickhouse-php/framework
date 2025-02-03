<?php

use Brickhouse\Support\StringHelper;

describe('StringHelper', function () {
    test('ellipsis', function (string $subject, int $length, string $expected) {
        $result = StringHelper::from($subject)->ellipsis($length);

        expect($result->__toString())->toBe($expected);
    })->with([
        ['', 10, ''],
        ['string', 10, 'string'],
        ['string', 1, 's...'],
        ['string', 4, 'string'],
        ['string', 3, 'str...'],
        ['string', 2, 'st...'],
        ['There\'s nothing wrong with having a tree as a friend.', 32, 'There\'s nothing wrong with havin...'],
        ['Let\'s get crazy.', 15, 'Let\'s get crazy.'],
    ]);

    test('end', function (string $subject, string $end, string $expected) {
        $result = StringHelper::from($subject)->end($end);

        expect($result->__toString())->toBe($expected);
    })->with([
        ['', '', ''],
        ['string', '/', 'string/'],
        ['string/', '/', 'string/'],
        ['string////', '/', 'string////'],
    ]);

    test('removeEnd', function (string $subject, string $end, string $expected) {
        $result = StringHelper::from($subject)->removeEnd($end);

        expect($result->__toString())->toBe($expected);
    })->with([
        ['', '', ''],
        ['string', '/', 'string'],
        ['string/', '/', 'string'],
        ['string////', '/', 'string///'],
        ['/string////', '/', '/string///'],
    ]);

    test('removeStart', function (string $subject, string $end, string $expected) {
        $result = StringHelper::from($subject)->removeStart($end);

        expect($result->__toString())->toBe($expected);
    })->with([
        ['', '', ''],
        ['string', '/', 'string'],
        ['/string', '/', 'string'],
        ['////string', '/', '///string'],
        ['/string////', '/', 'string////'],
    ]);

    test('replace', function (string $subject, string $search, string $replace, bool $caseSensitive, string $expected) {
        $result = StringHelper::from($subject)->replace($search, $replace, $caseSensitive);

        expect($result->__toString())->toBe($expected);
    })->with([
        ['', '', '', true, ''],
        ['String Value', 'String', 'Int', true, 'Int Value'],
        ['String Value', 'string', 'Int', true, 'String Value'],
        ['String Value', 'String', 'Int', false, 'Int Value'],
        ['String Value', 'string', 'Int', false, 'Int Value'],
    ]);

    test('slug', function (string $subject, string $expected) {
        $result = StringHelper::from($subject)->slug();

        expect($result->__toString())->toBe($expected);
    })->with([
        ['', ''],
        ['string', 'string'],
        ['String Value', 'string-value'],
        ['"', '&quot;'],
        ['&', '&amp;'],
        ['\'', '&#039;'],
        ['<', '&lt;'],
        ['>', '&gt;'],
    ]);

    test('start', function (string $subject, string $start, string $expected) {
        $result = StringHelper::from($subject)->start($start);

        expect($result->__toString())->toBe($expected);
    })->with([
        ['', '', ''],
        ['string', '/', '/string'],
        ['/string', '/', '/string'],
        ['////string', '/', '////string'],
    ]);

    test('snake', function (string $subject, string $expected) {
        $result = StringHelper::from($subject)->snake();

        expect($result->__toString())->toBe($expected);
    })->with([
        ['', ''],
        ['string', 'string'],
        ['String Value', 'string_value'],
        ['String   Value', 'string_value'],
        ['StringValue', 'string_value'],
        ["String\tValue", 'string_value'],
    ]);

    test('title', function (string $subject, string $expected) {
        $result = StringHelper::from($subject)->title();

        expect($result->__toString())->toBe($expected);
    })->with([
        ['', ''],
        ['string', 'String'],
        ['string value', 'String Value'],
        ['a string value', 'A String Value'],
        ['A String Value', 'A String Value'],
    ]);
})->group('support');
