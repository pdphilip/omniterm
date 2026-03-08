<?php

namespace OmniTerm\Tests;

use OmniTerm\OmniTermServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app): array
    {
        return [
            OmniTermServiceProvider::class,
        ];
    }
}
