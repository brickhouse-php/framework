<?php

namespace Brickhouse\Log\Handler;

use Brickhouse\Log\DefaultLogFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Level;

class ConsoleHandler extends StreamHandler
{
    /**
     * @param int|string|Level::*   $level  The minimum logging level at which this handler will be triggered
     * @param bool                  $bubble Whether the messages that are handled can bubble up the stack or not
     *
     * @phpstan-param value-of<Level::VALUES>|value-of<Level::NAMES>|Level $level
     */
    public function __construct(
        int|string|Level $level = Level::Debug,
        bool $bubble = true
    ) {
        parent::__construct(
            "php://stdout",
            $level,
            $bubble
        );

        $this->setFormatter(new DefaultLogFormatter);
    }
}
