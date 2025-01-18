<?php

namespace Brickhouse\Core;

interface Kernel
{
    /**
     * Invokes the kernel after the application has finished booting.
     *
     * @param   array<string,mixed>     $args   Optional arguments to pass to the kernel, if needed.
     *
     * @return void|int
     */
    public function invoke(array $args = []);
}
