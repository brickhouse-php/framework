<?php

namespace Brickhouse\Core\Console\Commands;

use Brickhouse\Config\Environment;
use Brickhouse\Console\Attributes\Option;
use Brickhouse\Console\Command;
use Brickhouse\Console\InputOption;
use Brickhouse\Core\Application;
use Brickhouse\Http\HttpKernel;

class Serve extends Command
{
    /**
     * The name of the console command.
     *
     * @var string
     */
    public string $name = 'serve';

    /**
     * The description of the console command.
     *
     * @var string
     */
    public string $description = 'Serves the application.';

    /**
     * The hostname to serve on.
     *
     * @var string
     */
    #[Option("host", input: InputOption::REQUIRED, description: 'Specify the hostname to serve on.')]
    public string $hostname = 'localhost';

    /**
     * The post to serve on.
     *
     * @var int
     */
    #[Option("port", input: InputOption::REQUIRED, description: 'Specify the port to serve on.')]
    public int $port = 8000;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $env = ucfirst(Environment::current());

        $debugMode = '';
        if (env('APP_DEBUG')) {
            $debugMode = ' (with <options=bold>debug mode enabled</>)';
        }

        $this->info("Server running on <span class='font-bold'>[http://{$this->hostname}:{$this->port}]</span>.");
        $this->notice("Currently running in <span class='font-bold'>{$env}</span> environment{$debugMode}.");
        $this->comment("Press Ctrl+C to stop the server.");

        return Application::current()->kernel(
            HttpKernel::class,
            [
                'hostname' => $this->hostname,
                'port' => $this->port,
            ]
        );
    }
}
