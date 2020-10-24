<?php

namespace AndrePolo\LaraDto\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Class MakeItem
 * @package AndrePolo\LaraDto\Console
 */
class MakeItem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ap-dto:make:item 
                            {name : the name for your DataTransferItem class} 
                            {--f|force}';

    /**
     * The console command description.
     *
     * @var string|null
     */
    protected $description = 'Create a new DataTransferItem';


    public function handle()
    {
        $generator = new ClassGenerator();

        $generator->name($this->argument('name'));
        $generator->type('item');

        $classname = $generator->fullClassName();
        $content = $generator->prepareContent('TransferItem');

        if (!$this->option('force') && class_exists($classname)) {
            $this->error('A class with the Name \'' . Str::studly($this->argument('name')) . '\' already exists');
            return 1;
        }

        $path = $generator->filePath();

        return $generator->storeFile($path, $content);
    }
}