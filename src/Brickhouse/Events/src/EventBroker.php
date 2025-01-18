<?php

namespace Brickhouse\Events;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;

final class EventBroker implements EventDispatcherInterface, ListenerProviderInterface
{
    use \Brickhouse\Events\Concerns\DispatchesEvents;
    use \Brickhouse\Events\Concerns\HandlesEventListeners;
}
