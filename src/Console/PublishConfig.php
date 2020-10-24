<?php

namespace AndrePolo\LaraDto\Console;

use Illuminate\Console\Command;

/**
 * Class PublishConfig
 * @package AndrePolo\LaraDto\Console
 */
class PublishConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ap-dto:publish:config';

    /**
     * The console command description.
     *
     * @var string|null
     */
    protected $description = 'Publishes the configuration file to your app config folder';


    public function handle()
    {
        $this->call('vendor:publish', ['--tag' => 'datatransfer-config']);
    }
}