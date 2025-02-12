<?php

namespace Brickhouse\Testing\Concerns;

use Brickhouse\Core\Application;

trait InteractsWithApplication
{
    protected null|Application $app = null;

    /**
     * Creates the application.
     *
     * @return Application
     */
    public function createApplication(): Application
    {
        return Application::create();
    }

    protected function setUpTestEnvironment(): void
    {
        if (!$this->app) {
            $this->app = $this->createApplication();
        }
    }

    protected function tearDownTestEnvironment(): void
    {
        if ($this->app !== null) {
            $this->app->terminate();
            $this->app = null;
        }
    }
}
