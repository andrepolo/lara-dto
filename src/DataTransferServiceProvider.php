<?php

namespace AndrePolo\LaraDto;

use AndrePolo\LaraDto\Console\MakeCollection;
use AndrePolo\LaraDto\Console\MakeItem;
use AndrePolo\LaraDto\Console\PublishConfig;
use AndrePolo\LaraDto\Tests\ExampleTestTransferItem;
use Illuminate\Support\ServiceProvider;

/**
 * Class DataTransferServiceProvider
 * @package AndrePolo\LaraDto
 */
class DataTransferServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands([
            MakeItem::class,
            MakeCollection::class,
            PublishConfig::class,
        ]);

        $this->app->bind('ExampleTestTransferItem', function () {
            return new ExampleTestTransferItem();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/datatransfer.php', 'datatransfer');

        $this->publishes([
            __DIR__ . '/../config/datatransfer.php' => config_path('datatransfer.php')
        ], ['datatransfer', 'datatransfer-config']);
    }
}