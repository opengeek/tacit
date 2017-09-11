<?php
/*
 * This file is part of the Tacit package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tacit\Test\Model\RethinkDB;

use DateTime;
use Tacit\Model\RethinkDB\Collection;
use Tacit\Test\Model\RethinkDB\NestedObject;

/**
 * Tests for Tacit\Model\Collection.
 *
 * @package Tacit\Test\Model
 */
class CollectionTest extends TestCase
{
    /**
     * Get fixture data starting with the specified index.
     *
     * @param int $idx The starting index value.
     *
     * @return array An array of associative arrays representing PersistentObject data.
     */
    protected static function fixtureData($idx = 1)
    {
        $data = [];
        for ($i = $idx; $i <= 10; $i++) {
            $data[] = [
                'name'  => "PersistentObject #{$i}",
                'text'  => "Text of PersistentObject #{$i}",
                'integer'   => $i,
                'float' => (float)"{$i}.{$i}",
                'date'  => new DateTime(),
                'password' => 'abcdefg',
                'arrayOfStrings' => ['string #' . (($i % 3) + 1)],
                'nestedObject' =>             [
                    'text' => "NestedObject of PersistentObject #{$i}",
                    'integer' => $i,
                    'array' => [
                        'key1' => 'value1',
                        'key2' => 'value2'
                    ]
                ]
            ];
        }
        return $data;
    }

    public function setUp()
    {
        parent::setUp();

        /* make sure we start with a clean collection container */
        \r\tableCreate(PersistentObject::collectionName())->run($this->fixture->getConnection()->getHandle());

        \r\table(PersistentObject::collectionName())->indexCreateMulti('arrayOfStrings')->run($this->fixture->getConnection()->getHandle());
        \r\table(PersistentObject::collectionName())->indexWait('arrayOfStrings')->run($this->fixture->getConnection()->getHandle());

        foreach (self::fixtureData() as $item) {
            PersistentObject::create($this->fixture, $item);
        }
    }

    public function tearDown()
    {
        /* remove all the collection items after the test */
        \r\tableDrop(PersistentObject::collectionName())->run($this->fixture->getConnection()->getHandle());

        parent::tearDown();
    }

    /**
     * Test Collection::getPublicVars() with an object instance.
     */
    public function testGetPublicVarsFromObject()
    {
        $object = PersistentObject::findOne($this->fixture, [], [], ['read_mode' => 'outdated']);

        $this->assertInstanceOf('Tacit\Test\Model\RethinkDB\PersistentObject', $object);
        $this->assertEquals(
            [
                'id', 'name', 'text', 'date', 'integer', 'float', 'boolean', 'password', 'arrayOfStrings', 'nestedObject'
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
            ['id', 'name', 'text', 'date', 'integer', 'float', 'boolean', 'password', 'arrayOfStrings', 'nestedObject'],
            array_keys(Collection::getPublicVars('Tacit\Test\Model\RethinkDB\PersistentObject'))
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
        $object = PersistentObject::findOne($this->fixture, [], [], ['read_mode' => 'outdated']);

        $this->assertInstanceOf('Tacit\Test\Model\RethinkDB\PersistentObject', $object);
        $this->assertEquals(
            $expected,
            array_values(Collection::getMask($object, $exclude))
        );
    }

    public function providerGetMaskFromObject()
    {
        return [
            [
                ['name', 'text', 'date', 'integer', 'float', 'boolean', 'arrayOfStrings', 'nestedObject'],
                []
            ],
            [
                ['text', 'date', 'integer', 'float', 'boolean', 'arrayOfStrings', 'nestedObject'],
                ['name']
            ],
            [
                ['text', 'date', 'integer', 'boolean', 'arrayOfStrings', 'nestedObject'],
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
            array_values(Collection::getMask('Tacit\Test\Model\RethinkDB\PersistentObject', $exclude))
        );
    }

    public function providerGetMaskFromClass()
    {
        return [
            [
                ['name', 'text', 'date', 'integer', 'float', 'boolean', 'arrayOfStrings', 'nestedObject'],
                []
            ],
            [
                ['text', 'date', 'integer', 'float', 'boolean', 'arrayOfStrings', 'nestedObject'],
                ['name']
            ],
            [
                ['text', 'date', 'integer', 'boolean', 'arrayOfStrings', 'nestedObject'],
                ['name', 'float']
            ],
        ];
    }

    /**
     * Test Collection::count()
     */
    public function testCount()
    {
        $all = PersistentObject::collection($this->fixture, ['read_mode' => 'outdated'])->count();
        $this->assertEquals(10, $all);
    }

    /**
     * Test Collection::distinct()
     */
    public function testDistinct()
    {
        $this->assertEquals(['string #1','string #2','string #3'], PersistentObject::collection($this->fixture, ['read_mode' => 'outdated'])->distinct('arrayOfStrings'));
    }

    /**
     * Test Collection::getConnection()
     */
    public function testGetConnection()
    {
        $this->assertInstanceOf('Tacit\Model\RethinkDB\Connection', PersistentObject::collection($this->fixture)->getConnection());
    }
}
