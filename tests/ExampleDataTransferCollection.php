<?php

namespace AndrePolo\LaraDto\Tests;

use AndrePolo\LaraDto\DataTransferCollection;

/**
 * Class ExampleDataTransferCollection
 * @package AndrePolo\LaraDto\Tests
 */
class ExampleDataTransferCollection extends DataTransferCollection
{
    /**
     * @var string
     */
    protected $itemClass = ExampleTransferItem::class;
}