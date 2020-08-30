<?php

namespace AndrePolo\DataTransfer\Tests;

use AndrePolo\DataTransfer\DataTransferItem;
use AndrePolo\DataTransfer\Exceptions\TypeMismatchException;
use Illuminate\Support\Arr;
use ReflectionException;

/**
 * Class DataTransferItemTest
 * @package AndrePolo\DataTransfer\Tests\Unit
 */
class DataTransferItemTest extends TestCase
{
    /**
     * @test
     * @throws ReflectionException
     */
    public function to_array_method_returns_an_array()
    {
        $item = new ExampleTransferItem();

        $this->assertIsArray($item->toArray());
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function to_array_method_returns_public_properties_only()
    {
        $item = (new ExampleTransferItem())->toArray();

        $this->assertArrayNotHasKey('privateProperty', $item);
        $this->assertArrayNotHasKey('protectedProperty', $item);
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function to_json_returns_a_string()
    {
        $item = new ExampleTransferItem();

        $this->assertIsString($item->toJson());
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function to_array_returns_given_values()
    {
        $item = new ExampleTransferItem();

        $array = [
            'nullProperty' => null,
            'stringProperty' => 'test string',
            'arrayProperty' => [
                'foo', 'bar'
            ]
        ];

        $item->fromArray($array);

        $result = $item->toArray();

        $this->assertArrayHasKey('nullProperty', $result);
        $this->assertNull(Arr::get($result, 'nullProperty'));

        $this->assertArrayHasKey('stringProperty', $result);
        $this->assertEquals('test string', Arr::get($result, 'stringProperty'));

        $this->assertArrayHasKey('arrayProperty', $result);
        $this->assertIsArray(Arr::get($result, 'arrayProperty'));
        $this->assertContains('foo', Arr::get($result, 'arrayProperty'));
        $this->assertContains('bar', Arr::get($result, 'arrayProperty'));
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function from_array_uses_item_full_qualified_class_name()
    {
        $item = new ExampleTransferItem();

        $data = [
            'fqcnItem' => [
                'a' => '1',
                'b' => '2',
                'c' => 3,
            ]
        ];

        $item->fromArray($data);

        $this->assertInstanceOf(DataTransferItem::class, $item->fqcnItem);
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function from_array_uses_item_aliased_class_name()
    {
        $item = new ExampleTransferItem();

        $data = [
            'classNameItem' => [
                'a' => '1',
                'b' => '2',
                'c' => 3,
            ]
        ];

        $item->fromArray($data);

        $this->assertInstanceOf(ExampleTestTransferItem::class, $item->classNameItem);
        $this->assertEquals(1, $item->classNameItem->a);
        $this->assertEquals(2, $item->classNameItem->b);
        $this->assertEquals(3, $item->classNameItem->c);
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function custom_setters_are_used_with_to_array()
    {
        config()->set('datatransfer.use_setter', true);

        $item = new ExampleTransferItem();

        $array = [
            'casted' => 'one,two,three'
        ];

        $item->fromArray($array);

        $result = $item->toArray();

        $this->assertIsArray(Arr::get($result, 'casted'));
        $this->assertContains('one', Arr::get($result, 'casted'));
        $this->assertContains('two', Arr::get($result, 'casted'));
        $this->assertContains('three', Arr::get($result, 'casted'));
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function custom_setters_are_not_used_with_to_array_when_config_is_set_to_false()
    {
        $item = new ExampleWithoutSettersTransferItem();

        $array = [
            'casted' => 'one,two,three'
        ];

        $item->fromArray($array);

        $result = $item->toArray();

        $this->assertIsNotArray(Arr::get($result, 'casted'));
        $this->assertEquals('one,two,three', Arr::get($result, 'casted'));
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function custom_getter_is_used_when_config_is_set_to_true()
    {
        $item = new ExampleTransferItem();

        $array = [
            'mapped' => [
                1, 2 ,3
            ]
        ];

        $item->fromArray($array);

        $result = $item->toArray();
        $mapped = Arr::get($result, 'mapped');

        $this->assertIsArray(Arr::get($result, 'mapped'));

        $this->assertEquals(2, array_shift($mapped));
        $this->assertEquals(4, array_shift($mapped));
        $this->assertEquals(6, array_shift($mapped));
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function handling_json_creates_proper_item()
    {
        $json = json_encode([
            'nullProperty' => null,
            'stringProperty' => 'test string',
            'arrayProperty' => [
                'foo', 'bar'
            ]
        ]);

        $item = new ExampleTransferItem();
        $item->fromJson($json);

        $this->assertNull($item->nullProperty);
        $this->assertEquals('test string', $item->stringProperty);
        $this->assertEquals(['foo','bar'], $item->arrayProperty);
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function schema_contains_the_correct_properties()
    {
        $item = new ExampleTransferItem();

        $schema = $item->schema();

        $this->assertArrayHasKey('nullProperty', $schema);
        $this->assertEquals('null', Arr::get($schema, 'nullProperty'));
        $this->assertArrayHasKey('stringProperty', $schema);
        $this->assertEquals('string', Arr::get($schema, 'stringProperty'));
        $this->assertArrayHasKey('arrayProperty', $schema);
        $this->assertEquals('array', Arr::get($schema, 'arrayProperty'));
        $this->assertArrayHasKey('casted', $schema);
        $this->assertEquals('array', Arr::get($schema, 'casted'));
        $this->assertArrayHasKey('mapped', $schema);
        $this->assertEquals('array', Arr::get($schema, 'mapped'));

        $key = 'AndrePolo\DataTransfer\Tests\ExampleTestTransferItem: fqcnItem';
        $this->assertArrayHasKey($key, $schema);
        $fqcnItem = Arr::get($schema, $key);
        $this->assertArrayHasKey('a', $fqcnItem);
        $this->assertEquals('string', Arr::get($fqcnItem, 'a'));
        $this->assertArrayHasKey('b', $fqcnItem);
        $this->assertEquals('string', Arr::get($fqcnItem, 'b'));
        $this->assertArrayHasKey('c', $fqcnItem);
        $this->assertEquals('array', Arr::get($fqcnItem, 'c'));

        $key = 'AndrePolo\DataTransfer\Tests\ExampleTestTransferItem: classNameItem';
        $this->assertArrayHasKey($key, $schema);
        $classNameItem = Arr::get($schema, $key);
        $this->assertArrayHasKey('a', $classNameItem);
        $this->assertEquals('string', Arr::get($classNameItem, 'a'));
        $this->assertArrayHasKey('b', $classNameItem);
        $this->assertEquals('string', Arr::get($classNameItem, 'b'));
        $this->assertArrayHasKey('c', $classNameItem);
        $this->assertEquals('array', Arr::get($classNameItem, 'c'));

        // public properties exist, so lets check the non existence of protected and private properties
        $this->assertArrayNotHasKey('privateProperty', $schema);
        $this->assertArrayNotHasKey('protectedProperty', $schema);
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function schema_contains_non_public_properties_as_well()
    {
        $item = new ExampleTransferItem();

        $schema = $item->schema(false);

        $this->assertArrayHasKey('privateProperty', $schema);
        $this->assertArrayHasKey('protectedProperty', $schema);
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function from_array_works_correctly_without_doc_blocks()
    {
        $item = new ExampleWithoutDocBlocks();

        $data = [
            'casted' => 1,
            'mapped' => [
                'foo',
                'bar'
            ]
        ];

        $item->fromArray($data);

        $this->assertEquals(1, $item->casted);
        $this->assertIsArray($item->mapped);
        $this->assertContains('foo', $item->mapped);
        $this->assertContains('bar', $item->mapped);
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function from_array_works_correctly_without_doc_blocks_in_strict_mode()
    {
        $item = new ExampleWithEmptyDocBlocks();

        $data = [
            'casted' => 1,
            'mapped' => [
                'foo',
                'bar'
            ]
        ];

        $item->fromArray($data, true);

        $this->assertEquals(1, $item->casted);
        $this->assertIsArray($item->mapped);
        $this->assertContains('foo', $item->mapped);
        $this->assertContains('bar', $item->mapped);
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function an_exception_is_thrown_when_data_type_does_not_match()
    {
        try {
            $item = new ExampleTransferItem();

            $array = [
                'nullProperty' => null,
                'stringProperty' => true,
                'arrayProperty' => 'string'
            ];

            $item->fromArray($array, true);

            $this->fail('expected TypeMismatchException has never been thrown');
        }
        catch (TypeMismatchException $exception) {
            $this->expectNotToPerformAssertions();
        }

    }
}