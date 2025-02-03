<?php

use Brickhouse\Tests;

pest()
    ->extend(Tests\Http\TestCase::class)
    ->in("Http");

pest()
    ->extend(Tests\Support\TestCase::class)
    ->in("Support");
