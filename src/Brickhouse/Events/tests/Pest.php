<?php

use Brickhouse\Events\Tests;

pest()
    ->extend(Tests\TestCase::class)
    ->in('Feature', 'Unit');
