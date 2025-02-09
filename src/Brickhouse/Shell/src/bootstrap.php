<?php

use Brickhouse\Core\Application;
use Brickhouse\Core\Composer;
use Brickhouse\Support\Collection;

class_alias(Application::class, 'Application');
class_alias(Collection::class, 'Collection');

$composer = new Composer(base_path());
$classMap = array_keys($composer->loader->getClassMap());

$aliasedNamespaces = [
    'App\Controllers',
    'App\Models',
];

foreach ($classMap as $className) {
    $isAliased = array_any(
        $aliasedNamespaces,
        fn(string $namespace) => str_starts_with($className, $namespace)
    );

    if (!$isAliased) {
        continue;
    }

    $baseClassName = substr($className, strripos($className, '\\'));
    $baseClassName = trim($baseClassName, '\\');

    class_alias($className, $baseClassName);
}
