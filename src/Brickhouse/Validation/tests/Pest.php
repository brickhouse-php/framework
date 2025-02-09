<?php

use Brickhouse\Validation\Tests;

pest()
    ->extend(Tests\TestCase::class)
    ->in('Feature', 'Unit');
