<?php

namespace Rockero\CI\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Rockero\CI\CIServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app): array
    {
        return [
            CIServiceProvider::class,
        ];
    }
}
