<?php

namespace Brickhouse\Core;

use Brickhouse\Container\Container;
use Brickhouse\Core\Console\Commands;
use Brickhouse\Core\Events;
use Brickhouse\Http\HttpKernel;
use Composer\Autoload\ClassLoader;

class Application extends Container
{
    use Concerns\HandlesEvents;
    use Concerns\HandlesExtensions;
    use Console\Concerns\HandlesCommands;

    /**
     * Gets the application's base path for path resolving.
     *
     * @var string
     */
    public string $basePath;

    /**
     * Gets the path to the projects `vendor`-folder.
     *
     * @var string
     */
    public string $vendorPath;

    /**
     * Gets the path to the applications source code.
     *
     * @var string
     */
    public string $appPath;

    /**
     * Gets the path to the applications configuration files.
     *
     * @var string
     */
    public string $configPath;

    /**
     * Gets the path to the applications local storage.
     *
     * @var string
     */
    public string $storagePath;

    /**
     * Gets the path to the applications resource storage.
     *
     * @var string
     */
    public string $resourcePath;

    /**
     * Gets the path to the applications public storage.
     *
     * @var string
     */
    public string $publicPath;

    /**
     * Gets the globally available application instance, if any.
     *
     * @var ?Application
     */
    protected static ?Application $instance;

    /**
     * Creates a new instance of `Application` and boots it immediately.
     *
     * @param   string                              $basePath
     */
    public final function __construct(string $basePath)
    {
        /**
         * @var Application $app
         * @phpstan-ignore varTag.nativeType
         */
        $app = $this;

        self::$container = $app;
        self::$instance = $app;

        $this->instance(Application::class, $this);

        $this->bootstrap($basePath);
        $this->boot();
    }

    /**
     * Deconstruct the application and reset global state.
     */
    public final function __destruct()
    {
        self::$container = null;
        self::$instance = null;
    }

    /**
     * Boot the given kernel on the application.
     *
     * @param   Kernel|class-string<Kernel>     $kernel     Kernel instance or class to boot.
     * @param   array<string,mixed>             $args       Optional arguments to pass to the kernel.
     *
     * @return int
     */
    public function kernel(Kernel|string $kernel, array $args = []): int
    {
        if (is_string($kernel)) {
            $kernel = $this->resolve($kernel);
        }

        // Register the kernel in the container
        $this->instance(Kernel::class, $kernel);

        $result = $kernel->invoke($args);
        if (is_int($result)) {
            return $result;
        }

        return 0;
    }

    /**
     * Sets the base path and all other deriving paths of the application.
     *
     * @param string $basePath
     *
     * @return void
     */
    private function setBasePath(string $basePath)
    {
        $this->basePath = $basePath;
        $this->vendorPath = path($this->basePath, "vendor");
        $this->appPath = path($this->basePath, env("APP_DIR", "src"));
        $this->configPath = path($this->basePath, env("APP_CONFIG_DIR", "config"));
        $this->storagePath = path($this->basePath, env("APP_STORAGE_DIR", "storage"));
        $this->publicPath = path($this->basePath, env("APP_PUBLIC_DIR", "public"));
    }

    /**
     * Initialize the discovery implementation.
     *
     * @return void
     */
    private function bootstrap(string $basePath)
    {
        $this->setBasePath($basePath);

        $this->initializeEventBroker();
        $this->initializeExtensions();

        event(new Events\ApplicationBootstrapping($this));

        $this->registerErrorHandler();

        $this->addCommands([
            Commands\ExtensionList::class,
            Commands\ModelGenerator::class,
            Commands\Notes::class,
            Commands\Serve::class,
        ]);

        event(new Events\ApplicationBootstrapped($this));
    }

    /**
     * Initialize the discovery implementation.
     *
     * @return void
     */
    private function boot()
    {
        event(new Events\ApplicationBooting($this));

        $this->bootExtensions();

        event(new Events\ApplicationBooted($this));
    }

    /**
     * Register the error handler.
     *
     * @return void
     */
    private function registerErrorHandler()
    {
        $reporting = error_reporting();

        // Disable user-triggered deprecation warnings.
        // amphp/http-server is using a deprecated method from another library which
        // makes Collision (or Woops) crash on every HTTP request.
        $reporting &= ~E_USER_DEPRECATED;

        // Disable internally-triggered deprecation warnings.
        $reporting &= ~E_DEPRECATED;

        error_reporting($reporting);

        if (env('APP_DEBUG', false)) {
            (new \NunoMaduro\Collision\Provider())->register();
        }
    }

    /**
     * Gets the current application instance.
     *
     * @return Application
     */
    public static function current(): Application
    {
        return self::$instance ?? throw new \Exception("No application available.");
    }

    /**
     * Creates a new `Application` instance.
     *
     * @param string|null   $basePath       Optional base path for all runtime path resolving.
     *
     * @return static
     */
    public static function create(?string $basePath = null): static
    {
        $basePath ??= self::inferBasePath();

        return new static($basePath);
    }

    /**
     * Infer the application's base directory from the environment.
     *
     * @return string
     */
    public static function inferBasePath()
    {
        return match (true) {
            isset($_ENV['APP_BASE_PATH']) => $_ENV['APP_BASE_PATH'],
            default => dirname(array_keys(ClassLoader::getRegisteredLoaders())[0]),
        };
    }

    /**
     * Reads the current request from superglobals, routes it's to the correct action, and sends the response back to the client.
     *
     * @return bool
     */
    public function handleRequest(): bool
    {
        // Invoking `Application::kernel` will both register and execute the kernel.
        // Within the HttpKernel, the HTTP request is read and handled, as well as sent back to the client.
        $this->kernel(HttpKernel::class);

        // Call the garbage collector to reduce the chances of it being triggered in the middle of a page generation.
        \gc_collect_cycles();

        return true;
    }

    /**
     * Determines whether the application is currently being executed as a worker.
     *
     * @return boolean
     */
    public function runningAsWorker(): bool
    {
        // FrankenPHP
        if (isset($_SERVER['FRANKENPHP_WORKER']) && function_exists('frankenphp_handle_request')) {
            return true;
        }

        return false;
    }

    /**
     * Enqueues the given callback as a handler for the current application worker.
     *
     * @param callable():void   $callback       Callback for the current worker.
     *
     * @return void
     */
    public function enqueueWorker(callable $callback, int $maxRequests = 0): void
    {
        $requestHandler = $this->workerRequestHandler();

        for ($nRequests = 0; !$maxRequests || $nRequests < $maxRequests; $nRequests++) {
            $keepRunning = $requestHandler($callback);

            if (!$keepRunning) {
                break;
            }
        }
    }

    /**
     * Gets the request handler for the current worker runtime.
     *
     * @return \Closure(callable():void $handler): bool
     */
    protected function workerRequestHandler(): \Closure
    {
        // FrankenPHP
        if (function_exists('frankenphp_handle_request')) {
            return fn(\Closure $callback) => \frankenphp_handle_request($callback);
        }

        throw new \RuntimeException(
            "Could not determine worker runtime. Are you sure the application is running in a worker?"
        );
    }
}
