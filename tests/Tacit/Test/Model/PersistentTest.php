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
 * Tests of the Tacit\Model\Persistent trait.
 *
 * @package Tacit\Test\Model
 */
class PersistentTest extends ModelTestCase
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
            MockPersistent::create($this->tacit->getContainer(), $item);
        }
    }

    public function tearDown()
    {
        MockPersistent::collection($this->fixture)->truncate();
    }

    /**
     * Test the MockPersistent::instance() method.
     *
     * @group model
     */
    public function testGetMockPersistentInstance()
    {
        $this->assertInstanceOf('Tacit\Test\Model\MockPersistent', MockPersistent::instance($this->tacit->getContainer(), []));
        $this->assertInstanceOf('Tacit\Test\Model\MockPersistent', MockPersistent::instance($this->tacit->getContainer(), [
            '_id' => 99999,
            'name' => 'an instance',
            'text' => 'an instance\'s text',
            'date' => new DateTime(),
            'integer' => 144,
            'float' => 3.14,
            'password' => 'password'
        ]));
    }

    /**
     * Test the MockPersistent::collection() method.
     *
     * @group model
     */
    public function testCollection()
    {
        $collection = MockPersistent::collection($this->fixture);
        $this->assertTrue($collection instanceof Collection);
        $this->assertInstanceOf('Tacit\Test\Model\MockCollection', $collection);
    }

    /**
     * Test the MockPersistent::collectionName() method.
     *
     * @group model
     */
    public function testCollectionName()
    {
        $this->assertEquals('mock_persistent', MockPersistent::collectionName());
    }

    /**
     * Test Count
     *
     * @param int            $expected
     * @param array|\Closure $criteria
     *
     * @dataProvider providerCount
     * @group model
     */
    public function testCount($expected, $criteria)
    {
        $this->assertEquals($expected, MockPersistent::count($this->tacit->getContainer(), $criteria));
    }
    public function providerCount()
    {
        return [
            [10, []],
            [1, ['name' => 'MockPersistent #1']],
            [1, ['name' => 'MockPersistent #9']],
        ];
    }

    /**
     * Test the MockPersistent::create() method.
     *
     * @param array $data
     *
     * @dataProvider providerCreate
     * @group model
     */
    public function testCreate($data)
    {
        try {
            $object = MockPersistent::create($this->tacit->getContainer(), $data);

            $this->assertNotNull($object);
            $this->assertInstanceOf('Tacit\Model\Persistent', $object);
            $this->assertInstanceOf('Tacit\Test\Model\MockPersistent', $object);
        } catch (ModelValidationException $e) {
            echo $e;
        }
    }
    /**
     * The dataProvider for testCreate().
     *
     * @return array[array]
     */
    public function providerCreate()
    {
        return [
            [
                [
                    '_id' => '8888',
                    'name' => 'create test',
                    'text' => 'this is a bunch of text for a create test',
                    'integer' => 13,
                    'float' => 3.14,
                    'date' => new DateTime(),
                    'boolean' => true,
                    'password' => sha1(uniqid(md5(mt_rand(0,999999999)), true)),
                    'arrayOfStrings' => [
                        'string #1',
                        'string #2',
                        'string #3'
                    ]
                ]
            ],
        ];
    }

    /**
     * Test the MockPersistent::instance() method.
     *
     * @param array $data An array of data to pass to the method.
     *
     * @dataProvider providerInstance
     * @group model
     */
    public function testInstance($data)
    {
        $object = MockPersistent::instance($this->tacit->getContainer(), $data);

        $this->assertNotNull($object);
        $this->assertInstanceOf('Tacit\Model\Persistent', $object);
        $this->assertInstanceOf('Tacit\Test\Model\MockPersistent', $object);
    }
    /**
     * dataProvider for testInstance()
     *
     * @return array
     */
    public function providerInstance()
    {
        return [
            [
                [
                    'name' => 'create test',
                    'text' => 'this is a bunch of text for a create test',
                    'integer' => 13,
                    'float' => 3.14,
                    'date' => new DateTime(),
                    'boolean' => false,
                    'password' => sha1(uniqid(md5(mt_rand(0,999999999)), true)),
                    'arrayOfStrings' => [
                        'string #1',
                        'string #2',
                        'string #3'
                    ]
                ]
            ]
        ];
    }

    /**
     * Test the MockPersistent::find() method.
     *
     * @param array $expected
     * @param array $criteria
     * @param array $fields
     *
     * @dataProvider providerFind
     * @group model
     */
    public function testFind($expected, $criteria, $fields)
    {
        $collection = MockPersistent::find($this->tacit->getContainer(), $criteria, []);
        array_walk($collection, function (&$value) use ($fields) {
            /** @var MockPersistent $value */
            $value = $value->toArray($fields);
        });

        $this->assertEquals($expected, array_values($collection));
    }
    /**
     * dataProvider for testFind().
     *
     * @return array
     */
    public function providerFind()
    {
        return [
            [
                [['name' => 'MockPersistent #1']],
                ['name' => 'MockPersistent #1'],
                ['name']
            ]
        ];
    }

    /**
     * Test the MockPersistent::findOne() method.
     *
     * @param array $expected
     * @param array $criteria
     * @param array $fields
     *
     * @dataProvider providerFindOne
     * @group model
     */
    public function testFindOne($expected, $criteria, $fields)
    {
        /** @var MockPersistent $object */
        $object = MockPersistent::findOne($this->tacit->getContainer(), $criteria, []);
        $this->assertEquals($expected, $object->toArray($fields));
    }
    /**
     * dataProvider for testFindOne().
     *
     * @return array
     */
    public function providerFindOne()
    {
        return [
            [
                ['name' => 'MockPersistent #1'],
                ['name' => 'MockPersistent #1'],
                ['name']
            ]
        ];
    }

    /**
     * Test MockPersistent::update().
     *
     * @param array          $expected
     * @param array|\Closure $criteria
     * @param array          $data
     *
     * @dataProvider providerUpdate
     * @group model
     */
    public function testUpdate($expected, $criteria, $data)
    {
        $this->assertEquals($expected, MockPersistent::update($this->tacit->getContainer(), $criteria, $data, []));
    }
    public function providerUpdate()
    {
        return [
            [
                1,
                ['name' => 'MockPersistent #1'],
                ['text' => 'Updated text for MockPersistent #1']
            ],
            [
                10,
                [],
                ['float' => 3.14]
            ],
            [
                10,
                [],
                ['float' => 3.14, 'boolean' => false]
            ],
        ];
    }
}
