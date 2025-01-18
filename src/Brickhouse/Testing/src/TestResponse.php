<?php

namespace Brickhouse\Testing;

use Brickhouse\Http\HttpStatus;
use Brickhouse\Http\Response;

class TestResponse extends Response
{
    public function __construct(Response $response)
    {
        parent::__construct(
            $response->status,
            $response->headers,
            $response->contentLength,
            $response->protocol
        );
    }

    /**
     * Asserts that the response gave the given status code.
     *
     * @param integer $status
     *
     * @return self
     */
    public function assertStatus(int $status): self
    {
        \PHPUnit\Framework\Assert::assertTrue(
            $this->status === $status,
            $this->assertionMessageStatusCode($status, $this->status)
        );

        return $this;
    }

    /**
     * Asserts that the response gave a successful (>=200 and <300) status code.
     *
     * @return self
     */
    public function assertSuccessful(): self
    {
        \PHPUnit\Framework\Assert::assertTrue(
            $this->isSuccessful(),
            $this->assertionMessageStatusCode('>=200, <300', $this->status)
        );

        return $this;
    }

    /**
     * Asserts that the response gave an OK (HTTP 200) status code.
     *
     * @return self
     */
    public function assertOk(): self
    {
        return $this->assertStatus(HttpStatus::OK);
    }

    /**
     * Asserts that the response gave a created (HTTP 201) status code.
     *
     * @return self
     */
    public function assertCreated(): self
    {
        return $this->assertStatus(HttpStatus::CREATED);
    }

    /**
     * Asserts that the response gave a bad request (HTTP 400) status code.
     *
     * @return self
     */
    public function assertBadRequest(): self
    {
        return $this->assertStatus(HttpStatus::BAD_REQUEST);
    }

    /**
     * Asserts that the response gave an unauthorized (HTTP 401) status code.
     *
     * @return self
     */
    public function assertUnauthorized(): self
    {
        return $this->assertStatus(HttpStatus::UNAUTHORIZED);
    }

    /**
     * Asserts that the response gave a not found (HTTP 404) status code.
     *
     * @return self
     */
    public function assertNotFound(): self
    {
        return $this->assertStatus(HttpStatus::NOT_FOUND);
    }

    /**
     * Asserts that the response gave a header with the given name. Optionally, also asserts the value of the header.
     *
     * @param   string  $headerName     Name of the header to assert the existence of.
     * @param   mixed   $value          Optionally, asserts the value of the header.
     *
     * @return self
     */
    public function assertHeader(string $headerName, mixed $value = null): self
    {
        $header = $this->headers->get($headerName);

        \PHPUnit\Framework\Assert::assertNotNull(
            $header,
            "Expected response to have header [{$headerName}] but it was not found."
        );

        if ($value !== null) {
            \PHPUnit\Framework\Assert::assertEquals(
                $header,
                $value,
                "Expected response to have header [{$headerName}] with value of [{$value}] but [{$header}] was found."
            );
        }

        return $this;
    }

    /**
     * Asserts that the response has no headers with the given name.
     *
     * @param   string  $headerName     Name of the header to assert the absence of.
     *
     * @return self
     */
    public function assertHeaderMissing(string $headerName): self
    {
        $header = $this->headers->get($headerName);

        \PHPUnit\Framework\Assert::assertNull(
            $header,
            "Expected response to be missing header [{$headerName}] but [{$header}] was found."
        );

        return $this;
    }

    /**
     * Get an assertion message for a status assertion containing extra details when available.
     *
     * @param  string|int  $expected
     * @param  string|int  $actual
     *
     * @return string
     */
    protected function assertionMessageStatusCode(string|int $expected, string|int $actual)
    {
        return "Expected response status code [{$expected}] but received {$actual}.";
    }
}
