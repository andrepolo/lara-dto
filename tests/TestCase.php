<?php

namespace AndrePolo\LaraDto\Tests;

use AndrePolo\LaraDto\DataTransferServiceProvider;

/**
 * Class TestCase
 * @package AndrePolo\LaraDto\Tests
 */
class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('datatransfer.class_namespace', 'AndrePolo\\LaraDto');
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            DataTransferServiceProvider::class
        ];
    }
}