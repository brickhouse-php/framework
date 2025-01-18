<?php

namespace Brickhouse\Core\Concerns;

use Brickhouse\Events\EventBroker;

trait HandlesEvents
{
    /**
     * Initializes the event broker.
     *
     * @return void
     */
    public function initializeEventBroker(): void
    {
        app()->singleton(EventBroker::class);
    }
}
