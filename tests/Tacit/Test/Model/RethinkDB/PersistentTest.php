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
use Tacit\Model\Exception\ModelValidationException;

/**
 * Test the MongaPersistent implementation.
 *
 * @package Tacit\Test\Model\RethinkDB
 */
class PersistentTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        /* make sure we start with a clean collection container */
        \r\tableCreate(PersistentObject::collectionName())->run($this->fixture->getConnection()->getHandle());

        \r\table(PersistentObject::collectionName())->indexCreateMulti('arrayOfStrings')->run($this->fixture->getConnection()->getHandle());
        \r\table(PersistentObject::collectionName())->indexWait('arrayOfStrings')->run($this->fixture->getConnection()->getHandle());
    }

    public function tearDown()
    {
        /* remove all the collection items after the test */
        \r\tableDrop(PersistentObject::collectionName())->run($this->fixture->getConnection()->getHandle());

        parent::tearDown();
    }

    /**
     * Test Persistent::collectionName()
     *
     * @group model
     * @group rethinkdb
     */
    public function testCollectionName()
    {
        $this->assertEquals('test_objects', PersistentObject::collectionName());
    }

    /**
     * Test Persistent::collection()
     *
     * @group model
     * @group rethinkdb
     */
    public function testCollection()
    {
        $this->assertInstanceOf('Tacit\Model\RethinkDB\Collection', PersistentObject::collection($this->fixture));
    }

    /**
     * Test Persistent::create()
     *
     * @param array $data The data used to create the object.
     *
     * @dataProvider providerCreate
     * @group model
     * @group rethinkdb
     */
    public function testCreate($data)
    {
        try {
            $object = PersistentObject::create($data, $this->fixture);

            $this->assertNotNull($object);
            $this->assertInstanceOf('Tacit\Model\Persistent', $object);
            $this->assertInstanceOf('Tacit\Model\RethinkDB\Persistent', $object);
            $this->assertInstanceOf('Tacit\Test\Model\RethinkDB\PersistentObject', $object);
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
                'date' => new DateTime(),
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
                'date' => new DateTime(),
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
