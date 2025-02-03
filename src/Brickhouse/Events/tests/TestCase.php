<?php

namespace Brickhouse\Events\Tests;

abstract class TestCase extends \Brickhouse\Testing\TestCase
{
    /**
     * Contains all the dispatched events in the test.
     *
     * @var array<int,array{0:string|object,1:mixed}>
     */
    private array $dispatchedEvents = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->dispatchedEvents = [];
    }

    /**
     * Creates a new mock listener to listen for `$event` and records it's dispatches.
     *
     * @param string|object     $eventName      Name of the event to assert.
     *
     * @return void
     */
    public function mockedListener(string|object $event): void
    {
        listen($event, fn(mixed $args) => $this->dispatchedEvents[] = [$event, $args]);
    }

    /**
     * Asserts that the given event was called `$amount` times.
     *
     * @param string|object     $event      Name of the event to assert.
     * @param int               $amount     Amount of times the event must be called. Defaults to `1`.
     *
     * @return self
     */
    public function assertDispatched(string|object $event, int $amount = 1): self
    {
        $matchingEvents = $this->getDispatchedEvents($event);
        $eventCount = count($matchingEvents);

        \PHPUnit\Framework\Assert::assertCount(
            $amount,
            $matchingEvents,
            "Expected event [{$event}] to be dispatched {$amount} time(s), but found {$eventCount}."
        );

        return $this;
    }

    /**
     * Asserts that the given event was called once with the given payload.
     *
     * @param string|object     $event      Name of the event to assert.
     * @param array             $payload    The payload to assert the event was dispatched with.
     *
     * @return self
     */
    public function assertDispatchedWith(string|object $event, mixed $payload): self
    {
        $matchingEvents = $this->getDispatchedEvents($event);
        $eventCount = count($matchingEvents);

        \PHPUnit\Framework\Assert::assertCount(
            1,
            $matchingEvents,
            "Expected event [{$event}] to be dispatched once, but found {$eventCount}."
        );

        \PHPUnit\Framework\Assert::assertEquals(
            $payload,
            $matchingEvents[0][1],
            "Expected payload for event [{$event}] to match given payload."
        );

        return $this;
    }

    /**
     * Asserts that the given event was not called.
     *
     * @param string|object     $event      Name of the event to assert.
     *
     * @return self
     */
    public function assertNotDispatched(string|object $event): self
    {
        $matchingEvents = $this->getDispatchedEvents($event);
        $eventCount = count($matchingEvents);

        \PHPUnit\Framework\Assert::assertEmpty(
            $matchingEvents,
            "Expected event [{$event}] to not be dispatched, but found {$eventCount} dispatches."
        );

        return $this;
    }

    /**
     * Asserts that no events were dispatched.
     *
     * @return self
     */
    public function assertNothingDispatched(): self
    {
        $count = count($this->dispatchedEvents);

        \PHPUnit\Framework\Assert::assertEmpty(
            $this->dispatchedEvents,
            "Expected no events to be dispatched, but found {$count} dispatches."
        );

        return $this;
    }

    /**
     * Gets all the dispatched events which has the given identifier.
     *
     * @param string|object $event
     *
     * @return array
     */
    private function getDispatchedEvents(string|object $event): array
    {
        return array_merge(array_filter($this->dispatchedEvents, fn(array $e) => $e[0] === $event));
    }
}
