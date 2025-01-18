<?php

namespace Brickhouse\Testing;

use Brickhouse\Testing\Concerns;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    use Concerns\InteractsWithApplication;
    use Concerns\InteractsWithHttpRequests;

    protected function setUp(): void
    {
        $this->setUpTestEnvironment();
    }

    protected function tearDown(): void
    {
        $this->tearDownTestEnvironment();
    }
}
