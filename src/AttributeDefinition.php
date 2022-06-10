<?php

namespace AndrePolo\LaraDto;

use Illuminate\Contracts\Container\BindingResolutionException;
use kamermans\Reflection\DocBlock;
use ReflectionProperty;

/**
 * Class AttributeDefinition
 * @package AndrePolo\LaraDto
 */
class AttributeDefinition extends DataTransferItem
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $var;

    /**
     * @var string
     */
    public $access;

    /**
     * @var string
     */
    public $type;

    /**
     * @var null|DataTransferItem|DataTransferCollection
     */
    public $class = null;

    /**
     * AttributeDefinition constructor.
     * @param ReflectionProperty $item
     * @throws BindingResolutionException
     */
    public function __construct(ReflectionProperty $item)
    {
        $this->name = $item->getName();
        $this->access = $this->getAccessValue($item);
        $this->var = $item->getType() ?? $this->getVarValue($item);
        $this->class = !$this->isPrimitive($this->var) ? $this->resolveClass($this->var) : null;
    }

    /**
     * @param ReflectionProperty $item
     *
     * @return string|null
     */
    protected function getAccessValue(ReflectionProperty $item)
    {
        if ($item->isPrivate()) {
            return 'private';
        }

        if ($item->isProtected()) {
            return 'protected';
        }

        if ($item->isPublic()) {
            return 'public';
        }

        return null;
    }

    /**
     * @param ReflectionProperty $item
     * @return string|null
     */
    protected function getVarValue(ReflectionProperty $item)
    {
        if (!$item->getDocComment()) {
            return null;
        }

        return optional(new DocBlock($item->getDocComment()))->getTag('var');
    }
}