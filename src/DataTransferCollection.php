<?php

namespace AndrePolo\LaraDto;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Arr;

/**
 * Class DataTransferItemCollection
 * @package AndrePolo\LaraDto
 */
abstract class DataTransferCollection implements Arrayable, Jsonable
{
    /**
     * @var string
     */
    protected $itemClass = DataTransferItem::class;

    /**
     * @var DataTransferItem[]
     */
    protected $items;

    /**
     * @return array
     */
    public function toArray()
    {
        return collect($this->items)->map(function (DataTransferItem $item) {
            return $item->toArray();
        })->toArray();
    }

    /**
     * @param array $data
     * @return $this
     */
    public function fromArray(array $data)
    {
        if (array_values($data) !== $data) {
            $data = [$data];
        }

        collect($data)->each(function (array $item) {
            /* @var DataTransferItem $class */
            $class = new $this->itemClass();
            $this->addItem($class->fromArray($item));
        });

        return $this;
    }

    /**
     * @param int $options
     *
     * @return false|string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * @param string $json
     *
     * @return DataTransferCollection
     */
    public function fromJson($json)
    {
        return $this->fromArray(json_decode($json, true));
    }

    /**
     * @return DataTransferItem
     */
    public function first()
    {
        return Arr::first($this->items);
    }

    /**
     * @return DataTransferItem
     */
    public function last()
    {
        return Arr::last($this->items);
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * @return $this
     */
    public function clear()
    {
        $this->items = [];

        return $this;
    }

    /**
     * @return array
     */
    public function schema()
    {
        return [];
    }

    /**
     * @param DataTransferItem $item
     *
     * @return $this
     */
    protected function addItem(DataTransferItem $item)
    {
        $this->items[] = $item;

        return $this;
    }
}
