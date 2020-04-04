<?php

namespace AndrePolo\DataTransfer;

use Illuminate\{Contracts\Container\BindingResolutionException,
    Contracts\Support\Arrayable,
    Contracts\Support\Jsonable,
    Support\Arr,
    Support\Str};
use kamermans\Reflection\DocBlock;
use ReflectionClass, ReflectionException, ReflectionProperty;

/**
 * Class DataTransferItem
 * @package AndrePolo\DataTransfer
 */
abstract class DataTransferItem implements Arrayable, Jsonable
{
    /**
     * @var array
     */
    protected $primitives = [
        '',
        'bool',
        'boolean',
        'int',
        'integer',
        'number',
        'float',
        'double',
        'string',
        'array',
        'object',
        'resource',
        'mixed',
        'callable',
    ];

    /**
     * @return array
     * @throws ReflectionException
     */
    public function toArray()
    {
        return collect($this->getReplectionProperties())->filter(function (ReflectionProperty $item) {
            return $item->isPublic();
        })->mapWithKeys(function(ReflectionProperty $item) {
            return [$item->getName() => $this->getAttribute($item->getName())];
        })->map(function ($item) {
            return $this->isTransfer($item) ? $item->toArray() : $item;
        })->toArray();
    }

    /**
     * @param array $data
     * @return $this
     * @throws ReflectionException
     */
    public function fromArray(array $data)
    {
        $attributes = $this->getAttributes();

        collect($data)->each(function ($value, $name) use ($attributes) {
            if (Arr::has($attributes, $name)) {
                $class = $this->resolveClass(Arr::get($attributes, $name));
                if (!is_null($class)) {
                    $value = $class->fromArray($value ?? []);
                }
            }

            $this->setAttribute($name, $value);
        });

        return $this;
    }

    /**
     * @param int $options
     *
     * @return false|string
     * @throws ReflectionException
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * @param string $json
     *
     * @return DataTransferItem
     * @throws ReflectionException
     */
    public function fromJson($json)
    {
        return $this->fromArray(json_decode($json, true));
    }

    /**
     * @param mixed $item
     * @return bool
     */
    protected function isTransfer($item)
    {
        return $item instanceof DataTransferItem || $item instanceof DataTransferCollection;
    }

    /**
     * @return mixed
     * @throws ReflectionException
     */
    protected function getAttributes()
    {
        return collect($this->getReplectionProperties())->mapWithKeys(function (ReflectionProperty $item) {
            return [$item->getName() => (new DocBlock($item->getDocComment()))->getTag('var')];
        })->filter(function ($item) {
            return !$this->isPrimitive($item);
        })->toArray();
    }

    /**
     * @return ReflectionProperty[]
     * @throws ReflectionException
     */
    protected function getReplectionProperties()
    {
        return (new ReflectionClass($this))->getProperties();
    }

    /**
     * @param $name
     *
     * @return mixed|null
     */
    protected function getAttribute($name)
    {
        $methodName = $this->getMethodName('get', $name);

        if (method_exists($this, $methodName) && $this->useGetter()) {
            return $this->{$methodName}();
        }

        if (property_exists($this, $name)) {
            return $this->{$name};
        }

        return null;
    }

    /**
     * @param string $name
     * @param $value
     *
     * @return |null
     */
    protected function setAttribute(string $name, $value)
    {
        $methodName = $this->getMethodName('set', $name);

        if (method_exists($this, $methodName) && $this->useSetter()) {
            return $this->{$methodName}($value);
        }

        if (property_exists($this, $name)) {
            $this->{$name} = $value;
        }

        return null;
    }

    /**
     * @param $name
     * @return mixed|null
     * @throws BindingResolutionException
     */
    protected function resolveClass($name)
    {
        if (app()->bound($name)) {
            return app()->make($name);
        }

        if (!class_exists($name) && !Str::contains($name, '\\')) {
            $name = implode('\\', [
                config()->get('datatransfer.class_namespace'),
                $name
            ]);
        }

        if (class_exists($name)) {
            return new $name;
        }

        return null;
    }

    /**
     * @param string $var
     * @return bool
     */
    protected function isPrimitive($var)
    {
        return is_null($var) || in_array($var, $this->primitives);
    }

    /**
     * @param string $type
     * @param string $name
     *
     * @return string
     */
    protected function getMethodName(string $type, string $name)
    {
        return implode('', [ucfirst($type), Str::studly($name)]);
    }

    /**
     * @return mixed
     */
    protected function useGetter()
    {
        return config()->get('datatransfer.use_getter', false);
    }

    /**
     * @return mixed
     */
    protected function useSetter()
    {
        return config()->get('datatransfer.use_setter', false);
    }
}