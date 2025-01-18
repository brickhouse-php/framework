<?php

namespace Brickhouse\Log;

use Monolog\Logger as Monolog;
use Psr\Log\LoggerInterface;

/**
 * @phpstan-import-type HandlerType from LogConfig
 * @phpstan-import-type Handlers from LogConfig
 */
class Logger implements LoggerInterface
{
    private readonly Monolog $instance;

    /**
     * Construct a new `Logger`-instance.
     *
     * @param string        $name       The unique name of the logger.
     * @param HandlerType[] $handlers   The handlers to attach to the logger.
     */
    protected function __construct(public readonly string $name, protected readonly array $handlers)
    {
        $this->instance = new Monolog($this->name, $this->handlers);
    }

    /**
     * Print a debug-level log message to the logger's channel.
     *
     * @param string    $message    Message to log, optionally formatted.
     * @param mixed[]   $context    Additional context for the log message.
     *
     * @return void
     */
    public function debug(string|\Stringable $message, array $context = []): void
    {
        $this->instance->debug($message, $context);
    }

    /**
     * Print an info-level log message to the logger's channel.
     *
     * @param string    $message    Message to log, optionally formatted.
     * @param mixed[]   $context    Additional context for the log message.
     *
     * @return void
     */
    public function info(string|\Stringable $message, array $context = []): void
    {
        $this->instance->info($message, $context);
    }

    /**
     * Print a notice-level log message to the logger's channel.
     *
     * @param string    $message    Message to log, optionally formatted.
     * @param mixed[]   $context    Additional context for the log message.
     *
     * @return void
     */
    public function notice(string|\Stringable $message, array $context = []): void
    {
        $this->instance->notice($message, $context);
    }

    /**
     * Print a warning-level log message to the logger's channel.
     *
     * @param string    $message    Message to log, optionally formatted.
     * @param mixed[]   $context    Additional context for the log message.
     *
     * @return void
     */
    public function warning(string|\Stringable $message, array $context = []): void
    {
        $this->instance->warning($message, $context);
    }

    /**
     * Print an error-level log message to the logger's channel.
     *
     * @param string    $message    Message to log, optionally formatted.
     * @param mixed[]   $context    Additional context for the log message.
     *
     * @return void
     */
    public function error(string|\Stringable $message, array $context = []): void
    {
        $this->instance->error($message, $context);
    }

    /**
     * Print a critical-level log message to the logger's channel.
     *
     * @param string    $message    Message to log, optionally formatted.
     * @param mixed[]   $context    Additional context for the log message.
     *
     * @return void
     */
    public function critical(string|\Stringable $message, array $context = []): void
    {
        $this->instance->critical($message, $context);
    }

    /**
     * Print an alert-level log message to the logger's channel.
     *
     * @param string    $message    Message to log, optionally formatted.
     * @param mixed[]   $context    Additional context for the log message.
     *
     * @return void
     */
    public function alert(string|\Stringable $message, array $context = []): void
    {
        $this->instance->alert($message, $context);
    }

    /**
     * Print an emergency-level log message to the logger's channel.
     *
     * @param string    $message    Message to log, optionally formatted.
     * @param mixed[]   $context    Additional context for the log message.
     *
     * @return void
     */
    public function emergency(string|\Stringable $message, array $context = []): void
    {
        $this->instance->emergency($message, $context);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed     $level      Defines which level to log at.
     * @param string    $message    Message to log, optionally formatted.
     * @param mixed[]   $context    Additional context for the log message.
     *
     * @return void
     */
    public function log(mixed $level, string|\Stringable $message, array $context = []): void
    {
        $this->instance->log($level, $message, $context);
    }

    /**
     * Gets the internal Monolog logger instance.
     *
     * @return Monolog
     */
    public function instance(): Monolog
    {
        return $this->instance;
    }

    /**
     * Build a new `Logger`-instance with the given handlers.
     *
     * @param Handlers      $handlers   The handlers to attach to the channel.
     * @param ?string       $name       An optional name to give to the logger.
     *
     * @return Logger
     */
    public static function build($handlers, ?string $name = null): Logger
    {
        return new Logger($name ?? uniqid("channel_"), array_wrap($handlers));
    }
}
