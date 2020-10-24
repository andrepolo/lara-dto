<?php

namespace AndrePolo\LaraDto\Tests;

use AndrePolo\LaraDto\DataTransferItem;

/**
 * Class TestTransferItem
 * @package AndrePolo\LaraDto
 */
class ExampleTransferItem extends DataTransferItem
{
    /**
     * @var null
     */
    public $nullProperty;

    /**
     * @var string
     */
    public $stringProperty;

    /**
     * @var array
     */
    public $arrayProperty;

    /**
     * @var \AndrePolo\LaraDto\Tests\ExampleTestTransferItem
     */
    public $fqcnItem;

    /**
     * @var ExampleTestTransferItem
     */
    public $classNameItem;

    /**
     * @var string
     */
    private $privateProperty;

    /**
     * @var string
     */
    protected $protectedProperty;

    /**
     * this property has its own setter which will be called on filling the object
     *
     * @var array
     */
    public $casted;

    /**
     * @param $value
     */
    public function setCasted($value)
    {
        $this->casted = explode(',', $value);
    }

    /**
     * @var array
     */
    public $mapped;

    /**
     * @return array
     */
    public function getMapped()
    {
        return collect($this->mapped)->map(function ($item) {
            return $item * 2;
        })->toArray();
    }

    /**
     * @return bool|mixed
     */
    protected function useSetter()
    {
        return true;
    }
}