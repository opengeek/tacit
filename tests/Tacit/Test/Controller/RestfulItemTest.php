<?php
/*
 * This file is part of the Tacit package.
 *
 * Copyright (c) MODX, LLC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tacit\Test\Controller;


use Tacit\Controller\Exception\RestfulException;
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
                'date'  => new \DateTime(),
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

        $this->mockEnvironment([
            'PATH_INFO' => '/collection/' . $itemObj->_id,
            'REQUEST_METHOD' => 'PUT',
            'CONTENT_TYPE' => 'application/json',
            'slim.input' => json_encode($data)
        ]);

        try {
            $response = $this->tacit->invoke();

            $item = json_decode($response->getBody(), true);

            unset($data['password']);

            $matches = array_uintersect_assoc($data, $item, array($this, 'compareMultidimensionalArray'));

            $this->assertEquals($data, $matches);

        } catch (RestfulException $e) {
            $this->fail($e->getMessage());
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    public function compareMultidimensionalArray($val1, $val2)
    {
        if (is_array($val1) && is_array($val2)) {
            $arr = array_uintersect_assoc($val1, $val2, array($this, 'compareMultidimensionalArray'));
            if (count($arr) == max(count($val1), count($val2))) return 0;
            return -1;
        }

        return strcmp($val1, $val2);
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
                    'date'  => (new \DateTime())->format(DATE_ISO8601),
                    'password' => 'uvwxyz',
                    'arrayOfStrings' => ['abc','def','ghi','jkl','mno']
                ]
            ]
        ];
    }
}
