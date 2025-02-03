<?php

use Brickhouse\Tests;

pest()
    ->extend(Tests\Http\TestCase::class)
    ->in("Http");
