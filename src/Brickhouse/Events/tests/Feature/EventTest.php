<?php

describe('Event broking', function () {
    it('dispatches event with string name', function () {
        $this->mockedListener("event-name");

        event("event-name");

        $this->assertDispatched("event-name");
    });

    it('doesnt dispatch event with mismatching string name', function () {
        $this->mockedListener("event-name");

        event("other-event-name");

        $this->assertNotDispatched("event-name");
    });

    it('dispatches event multiple times', function () {
        $this->mockedListener("event-name");

        event("event-name");
        event("event-name");
        event("event-name");

        $this->assertDispatched("event-name", 3);
    });

    it('dispatches event with arguments', function () {
        $this->mockedListener("event-name");

        event("event-name", ['1', 1, false]);

        $this->assertDispatchedWith("event-name", ['1', 1, false]);
    });

    it('dispatches event with class identifier', function () {
        $event = new class {
            public function __construct(public readonly string $item = 'hello') {}
        };

        $this->mockedListener($event::class);

        event($event);

        $this->assertDispatchedWith($event::class, $event);
    });
})->group('events');
