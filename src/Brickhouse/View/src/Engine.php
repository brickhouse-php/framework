<?php

namespace Brickhouse\View;

use Brickhouse\Http\Response;
use Brickhouse\Http\Transport\ContentType;

readonly class Engine implements \Brickhouse\View\Contracts\Engine
{
    /**
     * Gets a list of content types which the engine supports.
     *
     * @return list<string>
     */
    public function content(): array
    {
        return [
            ContentType::JSON,
            ContentType::HTML,
        ];
    }

    /**
     * Renders the view with the given alias.
     *
     * @param string                $alias  Alias of the view.
     * @param array<string,mixed>   $data   Optional data to pass to the view.
     *
     * @return Response
     */
    public function render(string $alias, array $data = []): Response
    {
        $extension = match (request()->format) {
            ContentType::JSON => ".php.json",
            default => ".php.html",
        };

        $viewPath = $alias;

        // If no extension was given in the view path, attempt to guess the correct one.
        if (trim(pathinfo($alias, PATHINFO_EXTENSION)) === '') {
            $viewPath .= $extension;
        }

        $view = view($viewPath, $data);

        return match (request()->format) {
            ContentType::JSON => Response::json($view->require()),
            default => Response::html($view->render()),
        };
    }
}
