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
        $formattedStack = join(" => ", $buildStack);

        parent::__construct($message . ' [' . $formattedStack . ']', 0, $previous);
    }
}
