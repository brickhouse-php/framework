<?php

namespace Brickhouse\Log;

use Brickhouse\Container\Exceptions\ResolutionFailedException;
use Brickhouse\Log\Handler\ConsoleHandler;
use Monolog\Handler\NullHandler;

class Log
{
    /**
     * Get the logger with the given channel name.
     *
     * @param string $channel
     *
     * @return Logger
     */
    public static function channel(string $channel): Logger
    {
        return self::logger($channel);
    }

    /**
     * Get the logger with the given channel name, if set. Otherwise, gets the default logger.
     *
     * @param string|null $channel
     *
     * @return Logger
     */
    private static function logger(?string $channel = null): Logger
    {
        try {
            $channel ??= resolve(LogConfig::class)->default;
            $loggerCache = resolve(LoggerCache::class);

            return $loggerCache->get($channel) ?? self::null();
        } catch (ResolutionFailedException) {
            return self::fallback();
        }
    }

    /**
     * Creates a new logger instance, which is meant to be used before the application is bootstrapped.
     *
     * @return Logger
     */
    private static function fallback(): Logger
    {
        return Logger::build([new ConsoleHandler()], "bootstrap");
    }

    /**
     * Creates a new logger instance, which does not output anything.
     *
     * @return Logger
     */
    private static function null(): Logger
    {
        return Logger::build([], "null");
    }

    /**
     * Print a debug-level log message to the default logger's channel.
     *
     * @param string    $message    Message to log, optionally formatted.
     * @param mixed[]   $context    Additional context for the log message.
     *
     * @return void
     */
    public static function debug(string $message, array $context = []): void
    {
        self::logger()->debug($message, $context);
    }

    /**
     * Print an info-level log message to the default logger's channel.
     *
     * @param string    $message    Message to log, optionally formatted.
     * @param mixed[]   $context    Additional context for the log message.
     *
     * @return void
     */
    public static function info(string $message, array $context = []): void
    {
        self::logger()->info($message, $context);
    }

    /**
     * Print a notice-level log message to the default logger's channel.
     *
     * @param string    $message    Message to log, optionally formatted.
     * @param mixed[]   $context    Additional context for the log message.
     *
     * @return void
     */
    public static function notice(string $message, array $context = []): void
    {
        self::logger()->notice($message, $context);
    }

    /**
     * Print a warning-level log message to the default logger's channel.
     *
     * @param string    $message    Message to log, optionally formatted.
     * @param mixed[]   $context    Additional context for the log message.
     *
     * @return void
     */
    public static function warning(string $message, array $context = []): void
    {
        self::logger()->warning($message, $context);
    }

    /**
     * Print an error-level log message to the default logger's channel.
     *
     * @param string    $message    Message to log, optionally formatted.
     * @param mixed[]   $context    Additional context for the log message.
     *
     * @return void
     */
    public static function error(string $message, array $context = []): void
    {
        self::logger()->error($message, $context);
    }

    /**
     * Print a critical-level log message to the default logger's channel.
     *
     * @param string    $message    Message to log, optionally formatted.
     * @param mixed[]   $context    Additional context for the log message.
     *
     * @return void
     */
    public static function critical(string $message, array $context = []): void
    {
        self::logger()->critical($message, $context);
    }

    /**
     * Print an alert-level log message to the default logger's channel.
     *
     * @param string    $message    Message to log, optionally formatted.
     * @param mixed[]   $context    Additional context for the log message.
     *
     * @return void
     */
    public static function alert(string $message, array $context = []): void
    {
        self::logger()->alert($message, $context);
    }

    /**
     * Print an emergency-level log message to the default logger's channel.
     *
     * @param string    $message    Message to log, optionally formatted.
     * @param mixed[]   $context    Additional context for the log message.
     *
     * @return void
     */
    public static function emergency(string $message, array $context = []): void
    {
        self::logger()->emergency($message, $context);
    }
}
