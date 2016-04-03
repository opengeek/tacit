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

/**
 * Test Restful controllers.
 *
 * @package Tacit\Test\Controller
 */
class RestfulControllerTest extends ControllerTestCase
{
    /**
     * Test a very basic RESTful GET request.
     *
     * @group controller
     */
    public function testGet()
    {
        $mock = $this->mockEnvironment([
            'REQUEST_URI' => '/'
        ]);

        $response = $this->tacit->invoke($mock);

        $this->assertEquals(
            array_intersect_assoc(
                ['message' => 'mock me do you?'],
                json_decode($response->getBody(), true)
            ),
            ['message' => 'mock me do you?']
        );
    }

    /**
     * Test a very simple RESTful POST request from JSON.
     *
     * @group controller
     */
    public function testPostFromJson()
    {
        $bodyRaw = ['message' => 'mock me do you mocker?'];

        $mock = $this->mockEnvironment([
            'REQUEST_URI' => '/',
            'REQUEST_METHOD' => 'POST',
            'CONTENT_TYPE' => 'application/json',
            'REQUEST_BODY' => '{"target":"mocker"}'
        ]);

        $response = $this->tacit->invoke($mock);

        $this->assertEquals(
            array_intersect_assoc(
                $bodyRaw,
                json_decode($response->getBody(), true)
            ),
            $bodyRaw
        );
    }

    /**
     * Test a very simple RESTful POST request.
     *
     * @group controller
     */
    public function testPostFromForm()
    {
        $bodyRaw = ['message' => 'mock me do you mocker?'];

        $mock = $this->mockEnvironment([
            'REQUEST_URI' => '/',
            'REQUEST_METHOD' => 'POST',
            'CONTENT_TYPE' => 'application/x-www-form-urlencoded',
            'REQUEST_BODY' => 'target=mocker'
        ]);

        $response = $this->tacit->invoke($mock);

        $this->assertEquals(
            array_intersect_assoc(
                $bodyRaw,
                json_decode($response->getBody(), true)
            ),
            $bodyRaw
        );
    }
}
