<?php

namespace Brickhouse\View\Helpers;

use Brickhouse\View\Engine\Helper;

abstract class AssetHelper implements Helper
{
    /**
     * Returns a preconnect tag to the hostname of the given URL.
     *
     * @param string $url
     *
     * @return string
     */
    protected function preconnect(string $url): string
    {
        if (!$this->isAbsoluteUrl($url)) {
            throw new \RuntimeException("Preconnect URL must start with http:// or https://");
        }

        $uri = \League\Uri\Uri::fromBaseUri($url);
        $hostname = $uri->getScheme() . '://' . $uri->getHost();

        return "<link rel=\"preconnect\" href=\"{$hostname}\">";
    }

    /**
     * Gets whether the given URL is an absolute URL or relative.
     *
     * @param string $url
     *
     * @return boolean
     */
    protected function isAbsoluteUrl(string $url): bool
    {
        if (!str_starts_with($url, "http://") && !str_starts_with($url, "https://")) {
            return false;
        }

        try {
            \League\Uri\Uri::fromBaseUri($url);
        } catch (\League\Uri\Exceptions\SyntaxError) {
            return false;
        }

        return true;
    }

    /**
     * Gets the asset URL to the asset of the given name or URL.
     *
     * @param string $asset
     *
     * @return string
     */
    protected function getAssetUrl(string $asset): string
    {
        if ($this->isAbsoluteUrl($asset)) {
            return $asset;
        }

        return path("_build", $asset);
    }
}
