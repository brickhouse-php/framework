<?php

namespace Brickhouse\View\Commands;

use Brickhouse\Console\Command;
use Brickhouse\View\Renderer;

class ViewsCompile extends Command
{
    /**
     * The name of the console command.
     *
     * @var string
     */
    public string $name = 'views:compile';

    /**
     * The description of the console command.
     *
     * @var string
     */
    public string $description = 'Compiles all the views in the project for production use.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        putenv("APP_ENV=production");

        $renderer = resolve(Renderer::class);

        $views = glob(view_path("**/*.php.html"));
        $count = count($views);

        foreach ($views as $view) {
            $this->debug("Compiling {$view}...");

            $template = @file_get_contents($view);
            $renderer->prerenderTemplate($template);
        }

        $this->info("Compiled all {$count} views in the application.");

        return 0;
    }
}
