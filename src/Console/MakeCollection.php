<?php

namespace AndrePolo\DataTransfer\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Class MakeItemCollection
 * @package AndrePolo\DataTransfer\Console
 */
class MakeCollection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'datatransfer:make:collection 
                            {name : the name for your DataTransferCollection class} 
                            {--i|item : If you pass this option, a DataTransferItem is also created}
                            {--f|force}';

    /**
     * The console command description.
     *
     * @var string|null
     */
    protected $description = 'Create a new DataTransferCollection';

    /**
     * @return int
     */
    public function handle()
    {
        $generator = new ClassGenerator();

        if ($this->option('item')) {
            $generator->type('item');
            $generator->itemName($this->argument('name'));

            $content = $generator->prepareContent('TransferItem');
            $classname = $generator->fullClassName();

            if (!class_exists($classname)) {
                $path = $generator->filePath();
                $generator->storeFile($path, $content);
            }

            $generator->useItem();
        }

        $generator->type('collection');
        $generator->name($this->argument('name'));
        $content = $generator->prepareContent('TransferCollection');
        $classname = $generator->fullClassName();

        if (!$this->option('force') && class_exists($classname)) {
            $this->error('A class with the Name \'' . Str::studly($this->argument('name')) . '\' already exists');
            return 1;
        }

        $path = $generator->filePath();

        return $generator->storeFile($path, $content) ? 0 : 1;
    }
}