<?php

namespace Brickhouse\Container\Exceptions;

use Psr\Container\ContainerExceptionInterface;

class ResolutionFailedException extends \Exception implements ContainerExceptionInterface
{
    /**
     * @param array<int,string>     $buildStack
     */
    public function __construct(
        string $message,
        public readonly array $buildStack,
        public readonly null|\Throwable $previous = null
    ) {
        parent::__construct($message, 0, $previous);
    }
}
