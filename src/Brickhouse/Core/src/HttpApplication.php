<?php

namespace Brickhouse\Core;

use Brickhouse\Http\HttpKernel;
use Brickhouse\Http\RequestFactory;
use Brickhouse\Http\Response;

class HttpApplication extends Application
{
    /**
     * Reads the current request from superglobals, routes it's to the correct action, and sends the response back to the client.
     *
     * @return void
     */
    public function handleRequest(): void
    {
        $requestFactory = resolve(RequestFactory::class);
        $request = $requestFactory->create();

        $kernel = resolve(HttpKernel::class);
        $response = $kernel->handle($request);

        $this->sendResponse($response);
    }

    /**
     * Sends the given response back to the client using PHP's internal HTTP functions.
     *
     * @param Response $response
     *
     * @return void
     */
    protected function sendResponse(Response $response): void
    {
        // Start the output buffering so that all output will be saved
        // to it instead of printing to console.
        ob_start();

        if (!headers_sent()) {
            // Send the status code first.
            http_response_code($response->status);

            // Then send all the headers.
            foreach ($response->headers->all() as $header => $value) {
                header($header . ': ' . $value);
            }
        }

        // Flush all the headers out to the buffer.
        ob_flush();

        // Write the response to the output buffer.
        while (($chunk = $response->content()->read()) !== null) {
            echo $chunk;
        }
        ob_flush();

        // Flush the buffer and stop the output buffering again.
        ob_end_flush();

        // If we're running FastCGI - as opposed to CLI - let it know the request is done.
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
    }
}
