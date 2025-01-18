<?php

namespace Brickhouse\Events\Concerns;

trait HandlesEventListeners
{
    use \Brickhouse\Events\Concerns\StoresEventState;

    /**
     * Registers an event listener to the given event.
     *
     * @param string|object     $event          Event key to listen for.
     * @param \Closure          $handler        Callback for when the event is dispatched.
     *
     * @return void
     */
    public function listen(string|object $event, \Closure $handler): void
    {
        $eventName = $this->serializeEvent($event);

        $this->addHandler($eventName, $handler);
    }
}
