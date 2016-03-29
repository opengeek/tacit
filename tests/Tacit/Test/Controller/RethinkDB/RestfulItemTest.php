<?php
/*
 * This file is part of the Tacit package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tacit\Test\Controller\RethinkDB;


use DateTime;
use Tacit\Controller\Exception\RestfulException;
use Tacit\Model\RethinkDB\Collection;
use Tacit\Test\Model\RethinkDB\PersistentObject;

class RestfulItemTest extends ControllerTestCase
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
                'boolean' => (bool)rand(0,1),
                'password' => 'abcdefg',
                'arrayOfStrings' => []
            ];
        }
        return $data;
    }

    protected function setUp()
    {
        parent::setUp();

        \r\tableCreate(PersistentObject::collectionName())->run($this->fixture->getConnection()->getHandle());

        foreach (self::fixtureData() as $item) {
            PersistentObject::create($item, $this->fixture);
        }
    }

    protected function tearDown()
    {
        \r\tableDrop(PersistentObject::collectionName())->run($this->fixture->getConnection()->getHandle());

        parent::tearDown();
    }

    /**
     * Test a GET request for a RestfulItem.
     *
     * @group controller
     * @group rethinkdb
     */
    public function testGet()
    {
        /** @var PersistentObject $itemObj */
        $itemObj = PersistentObject::findOne(['name' => 'MockPersistent #1'], [], $this->fixture);

        $mock = $this->mockEnvironment([
            'REQUEST_URI' => '/rethinkdb/collection/' . (string)$itemObj->id,
            'REQUEST_METHOD' => 'GET',
        ]);

        try {
            $response = $this->tacit->invoke($mock);

            $item = json_decode($response->getBody(), true);

            $data = $itemObj->toArray(Collection::getMask($itemObj));

            $matches = array_uintersect_assoc($data, $item, array($this, 'compareMultidimensionalArray'));

            $this->assertEquals($data, $matches);

        } catch (RestfulException $e) {
            $this->fail($e->getMessage());
        }
    }

    /**
     * Test a GET request for a RestfulItem with fields specified to return.
     *
     * @group controller
     * @group rethinkdb
     */
    public function testGetWithFields()
    {
        /** @var PersistentObject $itemObj */
        $itemObj = PersistentObject::findOne(['name' => 'MockPersistent #1'], [], $this->fixture);

        $mock = $this->mockEnvironment([
            'REQUEST_URI' => '/rethinkdb/collection/' . $itemObj->id,
            'QUERY_STRING' => 'fields=name,text',
            'REQUEST_METHOD' => 'GET',
        ]);

        try {
            $response = $this->tacit->invoke($mock);

            $item = json_decode($response->getBody(), true);

            $data = $itemObj->toArray(Collection::getMask($itemObj, ['integer', 'float', 'date', 'boolean', 'arrayOfStrings']));

            $matches = array_uintersect_assoc($data, $item, array($this, 'compareMultidimensionalArray'));

            $this->assertEquals($data, $matches);

        } catch (RestfulException $e) {
            $this->fail($e->getMessage());
        }
    }

    /**
     * Test a RESTful PUT request on a RestfulItem
     *
     * @param array $data
     *
     * @group controller
     * @group rethinkdb
     *
     * @dataProvider providerPut
     */
    public function testPut(array $data)
    {
        /** @var PersistentObject $itemObj */
        $itemObj = PersistentObject::findOne(['name' => $data['name']], [], $this->fixture);

        $mock = $this->mockEnvironment([
            'REQUEST_URI' => '/rethinkdb/collection/' . $itemObj->id,
            'REQUEST_METHOD' => 'PUT',
            'CONTENT_TYPE' => 'application/json',
            'slim.input' => json_encode($data)
        ]);

        try {
            $response = $this->tacit->invoke($mock);

            $item = json_decode($response->getBody(), true);

            unset($data['password']);

            $matches = array_uintersect_assoc($data, $item, array($this, 'compareMultidimensionalArray'));

            $this->assertEquals($data, $matches);

        } catch (RestfulException $e) {
            $this->fail($e->getMessage());
        }
    }

    public function providerPut()
    {
        return [
            [
                [
                    'name'  => "MockPersistent #1",
                    'text'  => "Text of MockPersistent #1",
                    'integer'   => 1,
                    'float' => 1.1,
                    'date'  => (new DateTime("@" . time()))->format(DATE_ISO8601),
                    'password' => 'uvwxyz',
                    'arrayOfStrings' => ['abc','def','ghi','jkl','mno']
                ]
            ],
            [
                [
                    'name'  => "MockPersistent #2",
                    'text'  => "Text of MockPersistent #2",
                    'integer'   => 2,
                    'float' => 2.2,
                    'date'  => (new DateTime("@" . time()))->format(DATE_ISO8601),
                    'boolean' => false,
                    'password' => 'uvwxyz',
                    'arrayOfStrings' => ['abc','def','ghi','jkl','mno']
                ],
            ]
        ];
    }
}
