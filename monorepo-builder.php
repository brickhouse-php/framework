<?php

declare(strict_types=1);

use Symplify\MonorepoBuilder\ComposerJsonManipulator\ValueObject\ComposerJsonSection;
use Symplify\MonorepoBuilder\Config\MBConfig;

return static function (MBConfig $mbConfig): void {
    $mbConfig->packageDirectories([__DIR__.'/src']);

    $mbConfig->dataToAppend([
        ComposerJsonSection::REQUIRE_DEV => [
            'phpstan/phpstan' => '^2.0',
        ],
        ComposerJsonSection::AUTOLOAD => [
            'exclude-from-classmap' => [
                '**/*.stub.php',
            ],
        ],
    ]);
};
