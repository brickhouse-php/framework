<?php

use Brickhouse\Support\Tests;

pest()
    ->extend(Tests\TestCase::class)
    ->in('Feature', 'Unit');
