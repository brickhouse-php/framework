<?php

namespace Brickhouse\View\Commands;

use Brickhouse\Console\Command;
use Brickhouse\View\Renderer;

class ViewsClear extends Command
{
    /**
     * The name of the console command.
     *
     * @var string
     */
    public string $name = 'views:clear';

    /**
     * The description of the console command.
     *
     * @var string
     */
    public string $description = 'Deletes all pre-compiled views from the build.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $views = glob(build_path("**/view-*.php"));
        $count = count($views);

        foreach ($views as $view) {
            unlink($view);
        }

        $this->info("Deleted all {$count} pre-compiled views in the application.");

        return 0;
    }
}
