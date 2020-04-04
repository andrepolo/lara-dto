<?php

namespace AndrePolo\DataTransfer\Tests;

use AndrePolo\DataTransfer\DataTransferServiceProvider;

/**
 * Class TestCase
 * @package AndrePolo\DataTransfer\Tests
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

        config()->set('datatransfer.class_namespace', 'AndrePolo\\DataTransfer');
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