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
use Tacit\Test\Model\MockPersistent;
use Tacit\Test\Model\MockRepository;

class RestfulCollectionTest extends ControllerTestCase
{
    /**
     * A MockRepository fixture for the RestfulCollection test cases.
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

        $this->fixture = $this->tacit->container->get('repository');
        foreach (self::fixtureData() as $item) {
            MockPersistent::create($item, $this->fixture);
        }
    }

    public function tearDown()
    {
        MockPersistent::collection($this->fixture)->truncate();
    }

    /**
     * Test a RESTful GET request on a RestfulCollection
     *
     * @group controller
     */
    public function testGet()
    {
        $this->mockEnvironment([
            'PATH_INFO' => '/collection/',
            'REQUEST_METHOD' => 'GET',
        ]);

        try {
            $response = $this->tacit->invoke();

            $result = json_decode($response->getBody(), true);

            $this->assertEquals(200, $response->getStatus());

            $this->assertArrayHasKey('_links', $result);
            $this->assertArrayHasKey('_embedded', $result);
            $this->assertArrayHasKey('total_items', $result);
            $this->assertArrayHasKey('returned_items', $result);
            $this->assertArrayHasKey('limit', $result);
            $this->assertArrayHasKey('offset', $result);

            $this->assertEquals(10, $result['total_items']);
            $this->assertEquals(10, $result['returned_items']);
            $this->assertEquals(25, $result['limit']);
            $this->assertEquals(0, $result['offset']);

        } catch (RestfulException $e) {
            $this->fail($e->getMessage());
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testGetEmptyCollection()
    {
        MockPersistent::collection($this->fixture)->truncate();

        $this->mockEnvironment([
            'PATH_INFO' => '/collection/',
            'REQUEST_METHOD' => 'GET',
        ]);

        try {
            $response = $this->tacit->invoke();

            $result = json_decode($response->getBody(), true);

            $this->assertEquals(200, $response->getStatus());

            $this->assertArrayHasKey('_links', $result);
            $this->assertArrayHasKey('_embedded', $result);
            $this->assertArrayHasKey('total_items', $result);
            $this->assertArrayHasKey('returned_items', $result);
            $this->assertArrayHasKey('limit', $result);
            $this->assertArrayHasKey('offset', $result);

            $this->assertEquals(0, $result['total_items']);
            $this->assertEquals(0, $result['returned_items']);
            $this->assertEquals(25, $result['limit']);
            $this->assertEquals(0, $result['offset']);

        } catch (RestfulException $e) {
            $this->fail($e->getMessage());
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }
    }
}
