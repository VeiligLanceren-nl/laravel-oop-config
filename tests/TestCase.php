<?php

namespace Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use VeiligLanceren\LaravelOopConfig\OopConfigServiceProvider;

abstract class TestCase extends BaseTestCase
{
    /**
     * @param $app
     * @return class-string[]
     */
    protected function getPackageProviders($app): array
    {
        return [OopConfigServiceProvider::class];
    }

    /**
     * @param $app
     * @return void
     */
    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('oop-config.test_case', TestCase::class);
    }
}
