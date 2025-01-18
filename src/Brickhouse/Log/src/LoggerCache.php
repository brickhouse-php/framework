<?php

namespace Brickhouse\Log;

final class LoggerCache
{
    /**
     * Gets all the logging channels in the cache.
     *
     * @var array<string,Logger>
     */
    private array $channels = [];

    /**
     * Add the given logging channel to the cache.
     *
     * @param string $name
     * @param Logger $channel
     *
     * @return self
     */
    public function add(string $name, Logger $channel): self
    {
        $this->channels[$name] = $channel;

        return $this;
    }

    /**
     * Gets the logging channel with the given name.
     *
     * @param string $name
     *
     * @return null|Logger
     */
    public function get(string $name): null|Logger
    {
        return $this->channels[$name] ?? null;
    }
}
