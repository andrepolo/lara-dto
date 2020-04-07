<?php

namespace AndrePolo\DataTransfer\Console;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

/**
 * Class StubGenerator
 * @package AndrePolo\DataTransfer\Console
 */
class StubGenerator
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $filename;

    /**
     * @var string
     */
    protected $classname;

    /**
     * @var string
     */
    protected $namespace;

    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var null|string
     */
    protected $item;

    /**
     * StubGenerator constructor.
     */
    public function __construct()
    {
        $this->config = config()->get('datatransfer');
        $this->prepareNamespace();
    }

    /**
     * @param $name
     */
    public function name($name)
    {
        $this->classname = ucfirst($name);
        $this->filename = $this->classname;
    }

    /**
     * @param $name
     */
    public function itemName($name)
    {
        $this->filename = ucfirst($name);

        if (preg_match('/(.*?)Collection$/i', $this->filename, $matches)) {
            $this->filename = implode('', [$matches[1], 'Item']);
            $this->classname = $this->filename;
        }
        else {
            $this->filename .= 'Item';
        }
    }

    /**
     * @param string $type
     */
    public function type($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function className()
    {
        return $this->classname;
    }

    /**
     * @return string
     */
    public function fullClassName()
    {
        return implode('\\', [
            $this->namespace,
            $this->classname()
        ]);
    }

    /**
     * @return string
     */
    public function filename()
    {
        if (is_null($this->filename)) {
            $this->filename = $this->className();
        }

        return $this->filename . '.php';
    }

    /**
     * @param $file
     *
     * @return string
     */
    public function prepareContent($file)
    {
        return $this->replaceVars(File::get(__DIR__ . '/../../stubs/' . $file . '.stub'));
    }

    /**
     * @return void
     */
    public function prepareNamespace()
    {
        $this->namespace = Arr::get($this->config, 'class_namespace', 'App\\DataTransfer');
    }

    /**
     */
    public function useItem()
    {
        $this->item = $this->classname;
    }

    /**
     * @param $path
     * @param $content
     *
     * @return bool|int
     */
    public function storeFile($path, $content)
    {
        File::ensureDirectoryExists($path);

        return File::put($path . '/' . $this->filename(), $content);
    }

    /**
     * @return mixed|string
     */
    public function filePath()
    {
        $path = $this->namespace;
        $path = str_replace(['App\\', '\\'], ['', '/'], $path);
        $path = app_path() . '/' . $path;

        return $path;
    }

    /**
     * @param $content
     * @return mixed
     */
    protected function replaceVars($content)
    {
        $search = [
            '_namespace_',
            '_classname_'
        ];
        $replace = [
            $this->namespace,
            $this->classname,
        ];

        if ($this->type === 'collection') {
            $search[] = '_itemclass_';
            $replace[] = $this->item ? 'protected $itemClass = ' . $this->item . '::class;' : '//';
        }

        $content = str_replace($search, $replace, $content);
        return $content;
    }
}