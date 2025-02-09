<?php

namespace Brickhouse\Shell\Commands;

use Brickhouse\Console\Command;
use Brickhouse\Console\Console;
use Brickhouse\Core\Application;
use Brickhouse\Database\Transposer\Model;
use Brickhouse\Shell\Casters;
use Brickhouse\Shell\CommandWrapper;
use Brickhouse\Support\Arrayable;
use Brickhouse\Support\Collection;
use Psy\Configuration;
use Psy\Shell as Psysh;

class Shell extends Command
{
    protected const array EXCLUDED_COMMANDS = [
        'help',
        'shell',
    ];

    /**
     * The name of the console command.
     *
     * @var string
     */
    public string $name = 'shell';

    /**
     * The description of the console command.
     *
     * @var string
     */
    public string $description = 'Starts an interactive shell for the application.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $config = new Configuration([
            'startupMessage' => <<<EOL

                        Welcome to the <fg=yellow>Brickhouse Shell</>!

                For more help, type <comment>help</comment> or visit <href=https://brickhouse-php.github.io/packages/shell/>the documentation.</>
                    To exit the shell, press <comment>Ctrl+D</comment> or type <comment>exit</comment>.

            EOL,
            'theme' => [
                'prompt' => 'sh> ',
                'bufferPrompt' => '... ',
                'replayPrompt' => '- ',
                'returnValue' => '=> ',
            ]
        ]);

        $config->getPresenter()->addCasters($this->getCasters());
        $config->addCommands($this->getCommands());

        $shell = new Psysh($config);
        $shell->setIncludes([
            path(Application::current()->vendorPath, 'composer', 'autoload_classmap.php'),
            __DIR__ . '/../bootstrap.php',
        ]);

        return $shell->run();
    }

    /**
     * Gets all the casters to define in the shell.
     *
     * @return array<class-string,callable(mixed $input):array>
     */
    protected function getCasters(): array
    {
        return [
            Application::class => Casters::castApplication(...),
            Collection::class => Casters::castCollection(...),
            Arrayable::class => Casters::castArrayable(...),
            Model::class => Casters::castTransposerModel(...),
        ];
    }

    /**
     * Gets all the commands to define in the shell.
     *
     * @return array<int,\Psy\Command\Command>
     */
    protected function getCommands(): array
    {
        $wrappedCommands = [];

        $console = new Console("Brickhouse");

        $commands = Collection::wrap(Application::current()->commands())
            ->map(fn(string $commandClass) => new $commandClass($console))
            ->sortBy('name')
            ->toArray();

        foreach ($commands as $command) {
            $command = new $command($console);

            // Ignore explicitly ignore commands
            if (in_array($command->name, self::EXCLUDED_COMMANDS)) {
                continue;
            }

            // Exclude serving command, as they will halt the entire shell.
            // Serving would also not make much sense in a REPL shell, like this.
            if (str_ends_with($command->name, 'serve')) {
                continue;
            }

            // Exclude installation commands as well, as most make use of prompts,
            // which wouldn't render correctly.
            if (str_starts_with($command->name, 'install')) {
                continue;
            }

            // We need to register the command with the console before we can execute it.
            $console->addCommand($command::class);

            $wrappedCommands[] = new CommandWrapper($command);
        }

        return $wrappedCommands;
    }
}
