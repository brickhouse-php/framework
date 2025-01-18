<?php

namespace Brickhouse\Console\Commands;

use Brickhouse\Console\Command;
use Brickhouse\Reflection\ReflectedType;

class Help extends Command
{
    /**
     * The name of the console command.
     *
     * @var string
     */
    public string $name = 'help';

    /**
     * The description of the console command.
     *
     * @var string
     */
    public string $description = 'Displays this help page.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->writeHtml(<<<HTML
            <p class='font-bold text-white mb-0'>
                Brickhouse
                <span class="text-zinc-400">
                    v1.0.0
                </span>
            </p>
        HTML);

        $this->writeHtml(<<<'HTML'
            <p class="mb-0">
                <span class="text-orange-300 mr-1 font-bold">
                    USAGE:
                </span>
                php brickhouse
                <span class="text-gray">&lt;command&gt; [options] [arguments]</span>
            </p>
        HTML);

        $this->writeHtml('<p class="text-orange-300 mr-1 mb-0 font-bold">Available commands:</p>');

        foreach ($this->groupCommands() as $commands) {
            foreach ($commands as $command) {
                $reflector = new ReflectedType($command);

                $name = $reflector->getProperty('name')->default;
                $description = $reflector->getProperty('description')->default;

                $this->writeHtml(<<<HTML
                    <div class="flex space-x-1 max-w-96 ml-4 mb-0 mt-0">
                        <span>
                            {$name}
                        </span>
                        <span class="flex-1 text-gray-400 text-right">
                            {$description}
                        </span>
                    </div>
                HTML);
            }

            $this->newline();
        }

        return 0;
    }

    /**
     * Group all commands in the application by their group name.
     *
     * @return array<string,list<class-string<Command>>>
     */
    protected function groupCommands(): array
    {
        $groups = [];

        foreach ($this->console->commands as $name => $command) {
            $groupName = explode(':', $name, limit: 2)[0];

            $groups[$groupName] ??= [];
            $groups[$groupName][] = $command;
        }

        foreach (array_keys($groups) as $idx) {
            $group = $groups[$idx];

            // Sort all the commands in the group.
            ksort($group, SORT_NATURAL);

            $groups[$idx] = $group;
        }

        // Sort all the groups by their name.
        ksort($groups, SORT_NATURAL);

        return $groups;
    }
}
