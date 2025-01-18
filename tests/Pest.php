<?php

use Brickhouse\Tests;

pest()
    ->extend(Tests\Database\TestCase::class)
    ->in("Database");

pest()
    ->extend(Tests\Events\TestCase::class)
    ->in("Events");

pest()
    ->extend(Tests\Http\TestCase::class)
    ->in("Http");

pest()
    ->extend(Tests\Support\TestCase::class)
    ->in("Support");
