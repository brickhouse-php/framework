<?php

namespace Brickhouse\View\Helpers;

use Brickhouse\View\Engine\Compiler;

class CssInclude extends AssetHelper
{
    /**
     * @inheritDoc
     */
    public string $tag = "css_include";

    /**
     * @inheritDoc
     */
    public function __invoke(Compiler $compiler, mixed ...$args): string
    {
        $stylesheet = $args[0] ?? throw new \RuntimeException("No stylesheet parameter given to @css_include");
        $stylesheetPath = $this->getAssetUrl($stylesheet);

        $tag = "<link href=\"{$stylesheetPath}\" rel=\"stylesheet\" />";

        if (isset($args['preconnect']) && $args['preconnect']) {
            return $this->preconnect($stylesheet) . $tag;
        }

        return $tag;
    }
}
