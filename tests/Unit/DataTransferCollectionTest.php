<?php

namespace AndrePolo\DataTransfer\Tests;

use Illuminate\Support\Arr;

/**
 * Class DataTransferCollectionTest
 * @package AndrePolo\DataTransfer\Tests
 */
class DataTransferCollectionTest extends TestCase
{
    /**
     * @test
     */
    public function the_collection_gets_filled_with_items()
    {
        $collection = new ExampleDataTransferCollection();

        $collection->fromArray($this->data());

        $this->assertEquals(3, $collection->count());
        $this->assertInstanceOf(ExampleTransferItem::class, $collection->first());
        /** @var ExampleTransferItem $first */
        $first = $collection->first();
        $this->assertNull($first->nullProperty);
        $this->assertEquals('string one', $first->stringProperty);
        $this->assertEquals(['foo1', 'bar1'], $first->arrayProperty);
    }

    /**
     * @test
     */
    public function collection_handles_single_array_correctly()
    {
        $collection = new ExampleDataTransferCollection();
        $data = Arr::first($this->data());

        $collection->fromArray($data);

        $this->assertEquals(1, $collection->count());
        $this->assertInstanceOf(ExampleTransferItem::class, $collection->first());
        /** @var ExampleTransferItem $first */
        $first = $collection->first();
        $this->assertNull($first->nullProperty);
        $this->assertEquals('string one', $first->stringProperty);
        $this->assertEquals(['foo1', 'bar1'], $first->arrayProperty);
    }

    /**
     * @test
     */
    public function handling_json_creates_proper_collection()
    {
        $json = json_encode($this->data());
        $collection = new ExampleDataTransferCollection();

        $collection->fromJson($json);

        $this->assertEquals(3, $collection->count());
        $this->assertInstanceOf(ExampleTransferItem::class, $collection->first());
        /** @var ExampleTransferItem $first */
        $first = $collection->first();
        $this->assertNull($first->nullProperty);
        $this->assertEquals('string one', $first->stringProperty);
        $this->assertEquals(['foo1', 'bar1'], $first->arrayProperty);
    }

    /**
     * @test
     */
    public function ensure_items_are_removed()
    {
        $collection = new ExampleDataTransferCollection();

        $collection->fromArray($this->data());

        $this->assertEquals(3, $collection->count());

        $collection->clear();

        $this->assertEquals(0, $collection->count());
    }

    /**
     * @return array
     */
    protected function data()
    {
        return [
            [
                'nullProperty'   => null,
                'stringProperty' => 'string one',
                'arrayProperty'  => [
                    'foo1', 'bar1'
                ],
            ],
            [
                'nullProperty'   => null,
                'stringProperty' => 'string two',
                'arrayProperty'  => [
                    'foo2', 'bar2'
                ],
            ],
            [
                'nullProperty'   => null,
                'stringProperty' => 'string three',
                'arrayProperty'  => [
                    'foo3', 'bar3'
                ],
            ]
        ];
    }
}