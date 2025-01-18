<?php

namespace Brickhouse\Container\Exceptions;

use Psr\Container\ContainerExceptionInterface;

class ContainerEntryMissingException extends \Exception implements ContainerExceptionInterface
{
    public function __construct(
        public readonly string $type,
    ) {
        parent::__construct("Container entry could not be found: '{$type}'.");
    }
}
