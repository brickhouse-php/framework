<?php

namespace Brickhouse\Http;

use Laminas\Diactoros\ServerRequestFactory;

final readonly class RequestFactory
{
    /**
     * Creates a new `Request`-instance from the current superglobals (`$_GET`, `$_POST`, etc).
     *
     * @return Request
     */
    public function create(): Request
    {
        $psrRequest = ServerRequestFactory::fromGlobals();

        return new Request(
            method: $psrRequest->getMethod(),
            uri: \League\Uri\Uri::fromBaseUri($psrRequest->getUri()->__toString()),
            headers: HttpHeaderBag::parseArray($psrRequest->getHeaders()),
            body: $psrRequest->getBody(),
            contentLength: $psrRequest->getBody()->getSize(),
            protocol: $psrRequest->getProtocolVersion(),
        );
    }
}
