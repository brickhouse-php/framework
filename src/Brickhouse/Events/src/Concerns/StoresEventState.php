<?php

namespace Brickhouse\Events\Concerns;

trait StoresEventState
{
    /**
     * Gets or sets all the event handlers in the store.
     *
     * @var array<string,array<string,\Closure>>
     */
    protected array $handlers = [];

    /**
     * Serialize the given event into a identifiable key for the event type.
     *
     * @param string|object $event
     *
     * @return string
     */
    protected function serializeEvent(string|object $event): string
    {
        return match (true) {
            $event instanceof \BackedEnum => $event->value,
            $event instanceof \UnitEnum => $event->name,
            is_string($event) => $event,
            default => $event::class,
        };
    }

    /**
     * Adds the given closure as a handler for the given event name.
     *
     * @param string        $eventName
     * @param \Closure      $closure
     *
     * @return void
     */
    protected function addHandler(string $eventName, \Closure $closure): void
    {
        if (!isset($this->handlers[$eventName])) {
            $this->handlers[$eventName] = [];
        }

        $handlerKey = spl_object_hash($closure);

        $this->handlers[$eventName][$handlerKey] = $closure;
    }

    /**
     * Gets the handlers attacthed to the given event name.
     *
     * @param string        $eventName
     *
     * @return array<int,\Closure>
     */
    protected function getHandlers(string $eventName): array
    {
        return array_values($this->handlers[$eventName] ?? []);
    }

    /**
     * Gets the handlers attacthed to the given event name.
     *
     * @param object    $event      An event for which to return the relevant listeners.
     *
     * @return iterable<callable>   An iterable (array, iterator, or generator) of callables.
     */
    public function getListenersForEvent(object $event): iterable
    {
        $name = $this->serializeEvent($event);

        return $this->getHandlers($name);
    }
}
