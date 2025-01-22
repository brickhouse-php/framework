<?php

namespace Brickhouse\View\Helpers;

use Brickhouse\Support\StringHelper;
use Brickhouse\View\Engine\Compiler;

class JsInclude extends AssetHelper
{
    /**
     * @inheritDoc
     */
    public string $tag = "js_include";

    /**
     * @inheritDoc
     */
    public function __invoke(Compiler $compiler, mixed ...$args): string
    {
        $script = $args[0] ?? throw new \RuntimeException("No script parameter given to @js_include");
        $scriptPath = $this->getAssetUrl($script);

        $arguments = StringHelper::from('')
            ->appendIf(fn() => isset($args['async']) && $args['async'], ' async')
            ->appendIf(fn() => isset($args['defer']) && $args['defer'], ' defer')
            ->__toString();

        $tag = "<script src=\"{$scriptPath}\"{$arguments}></script>";

        if (isset($args['preconnect']) && $args['preconnect']) {
            return $this->preconnect($script) . $tag;
        }

        return $tag;
    }
}
