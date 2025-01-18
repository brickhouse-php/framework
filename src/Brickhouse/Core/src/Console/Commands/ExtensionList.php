<?php

namespace Brickhouse\Core\Console\Commands;

use Brickhouse\Console\Command;

class ExtensionList extends Command
{
    /**
     * The name of the console command.
     *
     * @var string
     */
    public string $name = 'list:extensions';

    /**
     * The description of the console command.
     *
     * @var string
     */
    public string $description = 'List all the found extensions.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $extensions = app()->extensions();

        foreach ($extensions as $extension) {
            $className = $extension::class;
            $name = $extension->name;
            $version = $extension->version;

            $versionText = $version ? ", v{$version}" : "";

            $this->writeln("{$className} <fg=gray>({$name}{$versionText})</>");
        }

        return 0;
    }
}
