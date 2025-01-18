<?php

use Brickhouse\Events\EventBroker;

if (!function_exists("event")) {
    /**
     * Send the given event payload to the event broker.
     *
     * @param string|object                 $event      Event key to dispatch.
     * @param null|array<array-key,mixed>   $payload    Payload to pass to listeners. If `null`, passes `$event` instead.
     *
     * @return void
     */
    function event(string|object $event, null|array $payload = null): void
    {
        resolve(EventBroker::class)->dispatch($event, $payload);
    }
}

if (!function_exists("listen")) {
    /**
     * Listen for the given event using a closure.
     *
     * @param string|object     $event
     * @param \Closure          $handler
     *
     * @return void
     */
    function listen(string|object $event, \Closure $handler): void
    {
        resolve(EventBroker::class)->listen($event, $handler);
    }
}
