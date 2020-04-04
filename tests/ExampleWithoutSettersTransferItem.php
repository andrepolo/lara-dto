<?php

namespace AndrePolo\DataTransfer\Tests;

use AndrePolo\DataTransfer\DataTransferItem;

/**
 * Class TestTransferItem
 * @package AndrePolo\DataTransfer
 */
class ExampleWithoutSettersTransferItem extends DataTransferItem
{
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
     * @return bool|mixed
     */
    protected function useSetter()
    {
        return false;
    }
}