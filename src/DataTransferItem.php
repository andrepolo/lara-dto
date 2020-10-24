<?php

namespace AndrePolo\LaraDto;

use AndrePolo\LaraDto\Exceptions\TypeMismatchException;
use Illuminate\{Contracts\Container\BindingResolutionException,
    Contracts\Support\Arrayable,
    Contracts\Support\Jsonable,
    Support\Arr,
    Support\Collection,
    Support\Facades\Config,
    Support\Str
};
use kamermans\Reflection\DocBlock;
use ReflectionClass, ReflectionException, ReflectionProperty;

/**
 * Class DataTransferItem
 * @package AndrePolo\LaraDto
 */
abstract class DataTransferItem implements Arrayable, Jsonable
{
    /**
     * DataTransferItem constructor.
     *
     * @param array $data
     * @param bool $strict
     *
     * @throws ReflectionException
     */
    public function __construct(array $data = null, bool $strict = false)
    {
        if (!is_null($data)) {
            $this->fromArray($data, $strict);
        }
    }

    /**
     * @return array
     * @throws ReflectionException
     */
    public function toArray()
    {
        return $this->getAttributes()->filter(function (AttributeDefinition $item) {
            return $item->access === 'public';
        })->mapWithKeys(function (AttributeDefinition $item) {
            return [$item->name => !is_null($item->class) ? $item->class->toArray() : $this->getAttribute($item->name)];
        })->toArray();
    }

    /**
     * @param array $data
     * @param bool $strict
     *
     * @return $this
     * @throws ReflectionException
     */
    public function fromArray(array $data, $strict = false)
    {
        /* @var AttributeDefinition[] $attributes */
        $attributes = $this->getAttributes();

        collect($data)->each(function ($value, $name) use ($attributes, $strict) {
            /** @var AttributeDefinition $attribute */
            $attribute = Arr::get($attributes, $name);

            if ($this->strict($strict) && !$this->checkType($attribute, $value)) {
                throw new TypeMismatchException($attribute->var . ' does not match given type \'' . gettype($value) . '\'');
            }

            if (!is_null($attribute->class)) {
                $value = $attribute->class->fromArray($value, $strict);
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
     * @param bool $publicPropertiesOnly
     *
     * @return array
     * @throws ReflectionException
     */
    public function schema($publicPropertiesOnly = true)
    {
        $attributes = $this->getAttributes();

        if ($publicPropertiesOnly) {
            $attributes = $attributes->filter(function (AttributeDefinition $item) {
                return $item->access === 'public';
            });
        }
        return $attributes->mapWithKeys(function (AttributeDefinition $item) {
            $key = !is_null($item->class) ? get_class($item->class) . ': ' . $item->name : $item->name;
            $schema = !is_null($item->class) ? $item->class->schema() : $item->var;

            return [$key => $schema];
        })->toArray();
    }

    /**
     * @param int $options
     * @param bool $publicPropertiesOnly
     *
     * @return false|string
     * @throws ReflectionException
     */
    public function jsonSchema($options = 0, $publicPropertiesOnly = true)
    {
        return json_encode($this->schema($publicPropertiesOnly), $options);
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
     * @return Collection
     * @throws ReflectionException
     */
    protected function getAttributes()
    {
        return collect($this->getReflectionProperties())->mapWithKeys(function (ReflectionProperty $item) {
            return [$item->getName() => new AttributeDefinition($item)];
        });
    }

    /**
     * @return ReflectionProperty[]
     * @throws ReflectionException
     */
    protected function getReflectionProperties()
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
     * @return null|mixed
     * @throws ReflectionException
     */
    protected function setAttribute(string $name, $value)
    {
        $methodName = $this->getMethodName('set', $name);

        if (method_exists($this, $methodName) && $this->useSetter()) {
            return $this->{$methodName}($value);
        }

        if ($this->attributeIsPrivate($name)) {
            return null;
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
        if (is_null($name) || in_array($name, $this->config('primitives'))) {
            return $name;
        }

        if (app()->bound($name)) {
            return app()->make($name);
        }

        if (!class_exists($name) && !Str::contains($name, '\\')) {
            $name = implode('\\', [
                $this->config('class_namespace'),
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
        return is_null($var) || in_array($var, $this->config('primitives'));
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
     * @param ReflectionProperty $item
     * @return DocBlock
     */
    protected function getDocBlock(ReflectionProperty $item)
    {
        if (!$item->getDocComment()) {
            return null;
        }

        return (new DocBlock($item->getDocComment()));
    }

    /**
     * @param string $name
     *
     * @return bool
     * @throws ReflectionException
     */
    protected function attributeIsPrivate(string $name)
    {
        $attribute = new ReflectionProperty($this, $name);

        return $attribute->isPrivate();
    }

    /**
     * @param string $key
     * @param null $default
     *
     * @return mixed
     */
    protected function config(string $key, $default = null)
    {
        return Config::get("datatransfer.{$key}", $default);
    }

    /**
     * @param AttributeDefinition $item
     * @param $value
     *
     * @return bool
     */
    protected function checkType(AttributeDefinition $item, $value)
    {
        $should = $item->var;
        $is = gettype($value);
        $all = $this->config('primitives');

        if (!in_array($should, $all) || $should === 'mixed' || $is === 'unknown type') {
            return true;
        }

        switch ($should) {
            case 'bool':
            case 'boolean':
                return is_bool($value);

            case 'string':
                return is_string($value);

            case 'int':
            case 'integer':
                return is_int($value);

            case 'float':
            case 'double':
                return is_float($value);

            case 'array':
                return is_array($value);

            case 'object':
                return is_object($value);

            case 'callable':
                return is_callable($value);

            case 'resource':
                return is_resource($value);

            default:
                // maybe PHP adds types in the future, return true to not break it
                return true;
        }
    }

    /**
     * @return mixed
     */
    protected function useGetter()
    {
        return $this->config('use_getter', false);
    }

    /**
     * @return mixed
     */
    protected function useSetter()
    {
        return $this->config('use_setter', false);
    }

    /**
     * @param $strict
     * @return bool
     */
    protected function strict($strict)
    {
        return $strict || $this->config('strict');
    }
}