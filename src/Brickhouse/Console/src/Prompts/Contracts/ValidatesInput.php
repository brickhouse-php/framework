<?php

namespace Brickhouse\Console\Prompts\Contracts;

interface ValidatesInput
{
    /**
     * Validate the current input and return whether it's valid or not.
     *
     * @return null|string      `null` if the value is valid. Otherwise, an error message.
     */
    public function validate(): null|string;
}
