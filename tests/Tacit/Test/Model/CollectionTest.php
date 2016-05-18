<?php
/*
 * This file is part of the Tacit package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tacit\Test\Model;

use DateTime;
use Tacit\Model\Collection;
use Tacit\Model\Exception\ModelValidationException;

/**
 * Tests for Tacit\Model\Collection.
 *
 * @package Tacit\Test\Model
 */
class CollectionTest extends ModelTestCase
{
    /**
     * Get fixture data starting with the specified index.
     *
     * @param int $idx The starting index value.
     *
     * @return array An array of associative arrays representing MockPersistent data.
     */
    protected static function fixtureData($idx = 1)
    {
        $data = [];
        for ($i = $idx; $i <= 10; $i++) {
            $data[] = [
                'name'  => "MockPersistent #{$i}",
                'text'  => "Text of MockPersistent #{$i}",
                'integer'   => $i,
                'float' => (float)"{$i}.{$i}",
                'date'  => new DateTime(),
                'password' => 'abcdefg',
                'arrayOfStrings' => ['string #' . (($i % 3) + 1)]
            ];
        }
        return $data;
    }

    public function setUp()
    {
        parent::setUp();

        foreach (self::fixtureData() as $item) {
            MockPersistent::create($this->fixture, $item);
        }
    }

    public function tearDown()
    {
        MockPersistent::collection($this->fixture)->truncate();
    }

    /**
     * Test Collection::getPublicVars() with an object instance.
     */
    public function testGetPublicVarsFromObject()
    {
        $object = MockPersistent::findOne($this->fixture, [], []);

        $this->assertInstanceOf('Tacit\Test\Model\MockPersistent', $object);
        $this->assertEquals(
            [
                '_id', 'name', 'text', 'date', 'integer', 'float', 'boolean', 'password', 'arrayOfStrings'
            ],
            array_keys(Collection::getPublicVars($object))
        );
    }

    /**
     * Test Collection::getPublicVars() with a class name.
     */
    public function testGetPublicVarsFromClass()
    {
        $this->assertEquals(
            ['_id', 'name', 'text', 'date', 'integer', 'float', 'boolean', 'password', 'arrayOfStrings'],
            array_keys(Collection::getPublicVars('Tacit\Test\Model\MockPersistent'))
        );
    }

    /**
     * Test Collection::getMask() with an object instance.
     *
     * @param array $expected
     * @param array $exclude
     *
     * @dataProvider providerGetMaskFromObject
     */
    public function testGetMaskFromObject(array $expected, array $exclude)
    {
        $object = MockPersistent::findOne($this->fixture, [], []);

        $this->assertInstanceOf('Tacit\Test\Model\MockPersistent', $object);
        $this->assertEquals(
            $expected,
            array_values(Collection::getMask($object, $exclude))
        );
    }

    public function providerGetMaskFromObject()
    {
        return [
            [
                ['name', 'text', 'date', 'integer', 'float', 'boolean', 'arrayOfStrings'],
                []
            ],
            [
                ['text', 'date', 'integer', 'float', 'boolean', 'arrayOfStrings'],
                ['name']
            ],
            [
                ['text', 'date', 'integer', 'boolean', 'arrayOfStrings'],
                ['name', 'float']
            ],
        ];
    }

    /**
     * Test Collection::getMask() with an object instance.
     *
     * @param array $expected
     * @param array $exclude
     *
     * @dataProvider providerGetMaskFromClass
     */
    public function testGetMaskFromClass(array $expected, array $exclude)
    {
        $this->assertEquals(
            $expected,
            array_values(Collection::getMask('Tacit\Test\Model\MockPersistent', $exclude))
        );
    }

    public function providerGetMaskFromClass()
    {
        return [
            [
                ['name', 'text', 'date', 'integer', 'float', 'boolean', 'arrayOfStrings'],
                []
            ],
            [
                ['text', 'date', 'integer', 'float', 'boolean', 'arrayOfStrings'],
                ['name']
            ],
            [
                ['text', 'date', 'integer', 'boolean', 'arrayOfStrings'],
                ['name', 'float']
            ],
        ];
    }

    /**
     * Test Collection::count()
     */
    public function testCount()
    {
        $all = MockPersistent::collection($this->fixture)->count();
        $this->assertEquals(10, $all);
    }

    /**
     * Test Collection::distinct()
     */
    public function testDistinct()
    {
        $this->assertEquals(['string #1','string #2','string #3'], MockPersistent::collection($this->fixture)->distinct('arrayOfStrings'));
    }
}
