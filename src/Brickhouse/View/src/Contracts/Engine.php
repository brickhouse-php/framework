<?php

namespace Brickhouse\View\Contracts;

use Brickhouse\Http\Response;

interface Engine
{
    /**
     * Gets a list of content types which the engine supports.
     *
     * @return list<string>
     */
    public function content(): array;

    /**
     * Renders the view with the given alias.
     *
     * @param string                $alias  Alias of the view.
     * @param array<string,mixed>   $data   Optional data to pass to the view.
     *
     * @return Response
     */
    public function render(string $alias, array $data = []): Response;
}
