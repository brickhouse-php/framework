<?php

namespace Brickhouse\Core\Console\Commands;

use Brickhouse\Console\Attributes\Option;
use Brickhouse\Console\Command;
use Brickhouse\Console\InputOption;
use Brickhouse\Support\Collection;
use Symfony\Component\Finder\Finder;

class Notes extends Command
{
    /**
     * The name of the console command.
     *
     * @var string
     */
    public string $name = 'notes';

    /**
     * The description of the console command.
     *
     * @var string
     */
    public string $description = 'Finds notes and other annotated code in the project.';

    /**
     * Defines which annotations to look for.
     *
     * @var array<int,string>
     */
    #[Option("annotations", "a", "Defines which annotations to look for.", InputOption::REQUIRED)]
    public array $annotations = ["FIXME", "TODO"];

    /**
     * Defines which extensions to search in.
     *
     * @var array<int,string>
     */
    #[Option("extensions", "e", "Defines which extensions to search in.", InputOption::REQUIRED)]
    public array $extensions = ["php", "css", "html", "js"];

    /**
     * Folder names which are excluded from the search.
     *
     * @var array<int,string>
     */
    #[Option("excludes", null, "Folder names which are excluded from the search.", InputOption::REQUIRED)]
    public array $exclude = ["vendor", "node_modules"];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        /** @var array<string,list<array{path:string,line:int,annotation:string,comment:string}>> $annotations */
        $annotations = Collection::wrap($this->findAnnotations())
            ->groupBy("path")
            ->items();

        foreach ($annotations as $path => $fileAnnotations) {
            $this->writeHtml("<span class='text-black ml-1'>{$path}</span>");

            foreach ($fileAnnotations as ['line' => $line, 'annotation' => $annotation, 'comment' => $comment]) {
                $this->writeHtml("<span class='ml-3'>- [{$line}] [{$annotation}] {$comment}</span>");
            }

            $this->newline();
        }

        return 0;
    }

    /**
     * Find all the annotations in the source tree and return them.
     *
     * @return list<array{path:string,line:int,annotation:string,comment:string}>
     */
    protected function findAnnotations(): array
    {
        $pattern = sprintf("/\[(?<annotation>%s)\](?<comment>.*)$/Sm", join("|", $this->annotations));

        $finder = new Finder()
            ->in(base_path())
            ->exclude($this->exclude)
            ->ignoreVCS(true)
            ->ignoreDotFiles(true)
            ->ignoreUnreadableDirs(true)
            ->name(array_map(fn(string $ext) => '*.' . $ext, $this->extensions))
            ->files();

        $annotations = [];

        foreach ($finder as $file) {
            $path = $file->getRelativePathname();
            $content = $file->getContents();

            if (!preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
                continue;
            }

            for ($i = 0; isset($matches[0][$i]); $i++) {
                [$annotation, $position] = $matches['annotation'][$i];
                $comment = $matches['comment'][$i][0];

                $lineNo = substr_count($content, "\n", 0, $position) + 1;

                $annotations[] = [
                    "path" => $path,
                    "line" => $lineNo,
                    "annotation" => $annotation,
                    "comment" => $comment,
                ];
            }
        }

        return $annotations;
    }
}
