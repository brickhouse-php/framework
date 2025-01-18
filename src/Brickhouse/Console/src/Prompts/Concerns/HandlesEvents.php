<?php

namespace Brickhouse\Console\Prompts\Concerns;

trait HandlesEvents
{
    /**
     * The registered event listeners.
     *
     * @var array<string,list<\Closure>>
     */
    protected array $listeners = [];

    /**
     * Registers an event listener.
     */
    public function on(string $event, \Closure $callback): void
    {
        $this->listeners[$event][] = $callback;
    }

    /**
     * Emits an event.
     */
    public function emit(string $event, mixed ...$data): void
    {
        foreach ($this->listeners[$event] ?? [] as $listener) {
            $listener(...$data);
        }
    }
}
