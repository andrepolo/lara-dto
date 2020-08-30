<?php

namespace AndrePolo\DataTransfer\Tests;

use AndrePolo\DataTransfer\DataTransferCollection;

/**
 * Class ExampleDataTransferCollection
 * @package AndrePolo\DataTransfer\Tests
 */
class ExampleDataTransferCollection extends DataTransferCollection
{
    /**
     * @var string
     */
    protected $itemClass = ExampleTransferItem::class;
}