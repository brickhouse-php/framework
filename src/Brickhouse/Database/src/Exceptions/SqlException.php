<?php

namespace Brickhouse\Database\Exceptions;

class SqlException extends \PDOException
{
    public function __construct(
        public readonly string $query,
        public readonly array $bindings = [],
        null|\Throwable $previous = null
    ) {
        $message = "SQL statement failed to execute: `{$query}`";

        if (!empty($bindings)) {
            $message .= ' ' . json_encode($bindings);
        }

        if ($previous !== null) {
            $message .= ' (' . $previous->getMessage() . ')';
        }

        parent::__construct($message, 0, $previous);
    }
}
