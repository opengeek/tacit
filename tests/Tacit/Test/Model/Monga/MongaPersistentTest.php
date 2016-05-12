<?php
/*
 * This file is part of the Tacit package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tacit\Test\Model\Monga;

use MongoDate;
use Tacit\Model\Exception\ModelValidationException;

/**
 * Test the MongaPersistent implementation.
 *
 * @package Tacit\Test\Model\Monga
 */
class MongaPersistentTest extends MongaTestCase
{
    public function setUp()
    {
        parent::setUp();

        /* make sure we start with a clean collection container */
        MongaPersistentObject::collection($this->fixture)->truncate();
    }

    public function tearDown()
    {
        /* remove all the collection items after the test */
        MongaPersistentObject::collection($this->fixture)->truncate();
    }

    /**
     * Test MongaPersistent::collectionName()
     *
     * @group model
     * @group monga
     */
    public function testCollectionName()
    {
        $this->assertEquals('test_objects', MongaPersistentObject::collectionName());
    }

    /**
     * Test MongaPersistent::collection()
     *
     * @group model
     * @group monga
     */
    public function testCollection()
    {
        $this->assertInstanceOf('Tacit\Model\Monga\MongaCollection', MongaPersistentObject::collection($this->fixture));
    }

    /**
     * Test MongaPersistent::create()
     *
     * @param array $data The data used to create the object.
     *
     * @dataProvider providerCreate
     * @group model
     * @group monga
     */
    public function testCreate($data)
    {
        try {
            $object = MongaPersistentObject::create($this->container, $data);

            $this->assertNotNull($object);
            $this->assertInstanceOf('Tacit\Model\Persistent', $object);
            $this->assertInstanceOf('Tacit\Model\Monga\MongaPersistent', $object);
            $this->assertInstanceOf('Tacit\Test\Model\Monga\MongaPersistentObject', $object);
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
            [[
                'name' => 'create test',
                'text' => 'this is a bunch of text for a create test',
                'integer' => 13,
                'float' => 3.14,
                'date' => new MongoDate(),
                'password' => sha1(uniqid(md5(mt_rand(0,999999999)), true)),
                'arrayOfStrings' => [
                    'string #1',
                    'string #2',
                    'string #3'
                ]
            ]],
            [[
                'name' => 'create another test',
                'text' => 'this is a bunch more text for another create test',
                'integer' => 200999,
                'float' => 3.145673241,
                'date' => new MongoDate(),
                'boolean' => false,
                'password' => sha1(uniqid(md5(mt_rand(0,999999999)), true)),
                'arrayOfStrings' => [
                    'string #3',
                    'string #4',
                    'string #1'
                ]
            ]],
        ];
    }
}
