<?php
/*
 * This file is part of the Tacit package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tacit\Test\Controller;


use DateTime;
use Exception;
use Tacit\Controller\Exception\RestfulException;
use Tacit\Model\Collection;
use Tacit\Test\Model\MockPersistent;
use Tacit\Test\Model\MockRepository;

class RestfulItemTest extends ControllerTestCase
{
    /**
     * A MockRepository fixture for the RestfulItem test cases.
     *
     * @var MockRepository
     */
    public $fixture;

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
                'arrayOfStrings' => []
            ];
        }
        return $data;
    }

    public function setUp()
    {
        parent::setUp();

        $this->fixture = $this->tacit->getContainer()->get('repository');
        foreach (self::fixtureData() as $item) {
            MockPersistent::create($item, $this->fixture);
        }
    }

    public function tearDown()
    {
        MockPersistent::collection($this->fixture)->truncate();
    }

    /**
     * Test a GET request for a RestfulItem.
     */
    public function testGet()
    {
        /** @var MockPersistent $itemObj */
        $itemObj = MockPersistent::findOne(['name' => 'MockPersistent #1'], [], $this->fixture);

        $mock = $this->mockEnvironment([
            'REQUEST_URI' => '/collection/' . $itemObj->_id,
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
     */
    public function testGetWithFields()
    {
        /** @var MockPersistent $itemObj */
        $itemObj = MockPersistent::findOne(['name' => 'MockPersistent #1'], [], $this->fixture);

        $mock = $this->mockEnvironment([
            'PATH_INFO' => '/collection/' . $itemObj->_id,
            'QUERY_STRING' => 'fields=name,text',
            'REQUEST_METHOD' => 'GET',
        ]);

        try {
            $response = $this->tacit->invoke($mock);

            $item = json_decode($response->getBody(), true);

            $data = $itemObj->toArray(Collection::getMask($itemObj, ['integer', 'float', 'date', 'arrayOfStrings']));

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
     *
     * @dataProvider providerPut
     */
    public function testPut(array $data)
    {
        /** @var MockPersistent $itemObj */
        $itemObj = MockPersistent::findOne(['name' => $data['name']], [], $this->fixture);

        $mock = $this->mockEnvironment([
            'PATH_INFO' => '/collection/' . $itemObj->_id,
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
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testPartialPut()
    {
        /** @var MockPersistent $itemObj */
        $itemObj = MockPersistent::findOne(['name' => 'MockPersistent #1'], [], $this->fixture);

        $mock = $this->mockEnvironment([
            'PATH_INFO' => '/collection/' . $itemObj->_id,
            'REQUEST_METHOD' => 'PUT',
            'CONTENT_TYPE' => 'application/json',
            'slim.input' => json_encode([
                'name' => 'New MockPersistent #1',
                'password' => 'new_password'
            ])
        ]);

        $response = $this->tacit->invoke($mock);

        $item = json_decode($response->getBody(), true);

        $this->assertArrayHasKey('name', $item);
        $this->assertEquals('New MockPersistent #1', $item['name']);
        $this->assertNull($item['text']);
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
                    'date'  => (new DateTime())->format(DATE_ISO8601),
                    'password' => 'uvwxyz',
                    'arrayOfStrings' => ['abc','def','ghi','jkl','mno']
                ],
                [
                    'name'  => "MockPersistent #2",
                    'text'  => "Text of MockPersistent #2",
                    'integer'   => 2,
                    'float' => 2.2,
                    'date'  => (new DateTime())->format(DATE_ISO8601),
                    'boolean' => false,
                    'password' => 'uvwxyz',
                    'arrayOfStrings' => ['abc','def','ghi','jkl','mno']
                ],
            ]
        ];
    }
}
