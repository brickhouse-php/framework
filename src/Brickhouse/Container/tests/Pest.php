<?php

use Brickhouse\Container\Tests\TestCase;

pest()
    ->extend(TestCase::class)
    ->in('Unit', 'Feature');
