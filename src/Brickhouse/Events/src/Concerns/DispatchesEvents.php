<?php

namespace Brickhouse\Events\Concerns;

use Psr\EventDispatcher\StoppableEventInterface;

trait DispatchesEvents
{
    use \Brickhouse\Events\Concerns\StoresEventState;

    /**
     * Dispatch a new event.
     *
     * @param string|object                 $event      Event key to dispatch.
     * @param null|array<array-key,mixed>   $payload    Payload to pass to listeners. If `null`, passes `$event` instead.
     *
     * @return string|object
     */
    public function dispatch(string|object $event, null|array $payload = null): string|object
    {
        $eventName = $this->serializeEvent($event);
        $handlers = $this->getHandlers($eventName);

        $payload ??= $event;

        foreach ($handlers as $handler) {
            $handler($payload);

            // To adhere to PSR-14, we must stop further listeners from being called, if the
            // event class requests it.
            if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
                break;
            }
        }

        return $event;
    }
}
