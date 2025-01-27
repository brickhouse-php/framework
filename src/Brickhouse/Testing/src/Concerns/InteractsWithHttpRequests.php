<?php

namespace Brickhouse\Testing\Concerns;

use Brickhouse\Http\HttpKernel;
use Brickhouse\Http\Request;
use Brickhouse\Http\Transport\Uri;
use Brickhouse\Testing\TestResponse;

trait InteractsWithHttpRequests
{
    /**
     * Sends an HTTP GET request to the application at the given path.
     *
     * @return TestResponse
     */
    protected function get(string $path): TestResponse
    {
        return $this->call('GET', $path);
    }

    /**
     * Sends an HTTP POST request to the application at the given path.
     *
     * @return TestResponse
     */
    protected function post(string $path): TestResponse
    {
        return $this->call('POST', $path);
    }

    /**
     * Create an in-memory SQLite database connection.
     *
     * @return TestResponse
     */
    private function call(string $method, string $path): TestResponse
    {
        $kernel = resolve(HttpKernel::class);

        $request = new Request(
            $method,
            new Uri()->withPath($path)
        );

        $response = $kernel->handle($request);
        $testResponse = new TestResponse($response);

        return $testResponse;
    }
}
