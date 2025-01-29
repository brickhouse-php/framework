<?php

namespace Brickhouse\View;

use Brickhouse\Http\Response;
use Brickhouse\Http\Responses\NotFound;
use Brickhouse\Http\Transport\ContentType;
use Brickhouse\View\Engine\Exceptions\ViewNotFoundException;

class ViewFactory
{
    /**
     * Defines all the view engines in the factory.
     *
     * @var list<\Brickhouse\View\Contracts\Engine>
     */
    private readonly array $engines;

    public function __construct(\Brickhouse\View\Contracts\Engine ...$engines)
    {
        $this->engines = $engines;
    }

    /**
     * Renders a new view from the given alias and data bag.
     *
     * @param string                $alias      Alias of the view to render.
     * @param array<string,mixed>   $data       Optional data to pass to the view.
     *
     * @return Response
     */
    public function render(string $alias, array $data = []): Response
    {
        foreach ($this->resolveEngineForRequest() as $engine) {
            try {
                return $engine->render($alias, $data);
            } catch (ViewNotFoundException) {
                // If no matching view was found for the engine, continue to the next engine.
                continue;
            }
        }

        // If no explicit view was found for a JSON request, we can implicitly
        // send the given data back as formatted JSON without needing a view.
        if (request()->format === ContentType::JSON) {
            // If the data array is indexed and only contains a single item,
            // unwrap it and set that back in the JSON response.
            if (array_is_list($data) && count($data) === 1) {
                $data = reset($data);
            }

            return Response::json($data);
        }

        // Attempt to find a view which has the same alias, but a different extension.
        $view = $this->findFallbackView($alias, $data);
        if ($view === null) {
            return new NotFound();
        }

        // If we're sending back a JSON template, do it in a JSON response.
        if (str_ends_with($view->path, '.json')) {
            return Response::json($view->require());
        }

        return Response::html($view->render());
    }

    /**
     * Resolves the most fitting engine for the current request.
     *
     * By default, it picks an engine which supports the highest priority format in the `Accept` header.
     * If the header has no `Accept`-header, returns the first engine.
     * If no engines fits the request, `null` is returned.
     *
     * @return \Generator<int,\Brickhouse\View\Contracts\Engine,void,void>
     */
    protected function resolveEngineForRequest()
    {
        if (empty($this->engines)) {
            throw new \InvalidArgumentException("No view engines registered.");
        }

        $acceptBag = request()->headers->accept();

        if ($acceptBag === null) {
            yield current($this->engines);
            return;
        }

        // The items from `AcceptBag::all()` are pre-sorted by quality.
        foreach ($acceptBag->all() as $formatItem) {
            foreach ($this->engines as $engine) {
                $supportsContentType = array_any(
                    $engine->content(),
                    $formatItem->matches(...)
                );

                if ($supportsContentType) {
                    yield $engine;
                }
            }
        }
    }

    /**
     * Creates a new `View`-instance which is named after the same alias, but potentially a different extension.
     *
     * @param string                $alias  Alias of the view.
     * @param array<string,mixed>   $data   Optional data to pass to the view.
     *
     * @return null|View
     */
    protected function findFallbackView(string $alias, array $data = []): null|View
    {
        // Strip the extension from the view alias, if one was given.
        if (str_contains(pathinfo($alias, PATHINFO_FILENAME), ".")) {
            $alias = preg_replace("/\.\w+\.php$/", "", $alias, limit: 1);
        }

        $candidates = glob(view_path($alias . '.*.php'));
        if (empty($candidates)) {
            return null;
        }

        // The `view` function needs the path to be relative, so we must replace the absolute path
        // with a relative one.
        $relativeViewPath = basename($candidates[0]);

        // If a directory was named in `$alias`, prepend it onto the relative path.
        if (($directory = pathinfo($alias, PATHINFO_DIRNAME))) {
            $relativeViewPath = $directory . DIRECTORY_SEPARATOR . $relativeViewPath;
        }

        return view($relativeViewPath, $data);
    }
}
