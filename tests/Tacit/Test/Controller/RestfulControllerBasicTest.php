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


use Tacit\Tacit;

class RestfulControllerBasicTest extends ControllerTestCase
{
    /**
     * Test a very basic RESTful GET request with Basic authentication.
     *
     * @group controller
     */
    public function testGet()
    {
        $bodyRaw = ['message' => 'mock me do you?'];
        $clientKey = 'cb892ecb-6458-425c-9e3a-b3e99ec86f56';
        $secretKey = '4M2U1KSlv0jmqLAgs118fq4dugd534eP';

        $mock = $this->mockEnvironment([
            'PATH_INFO' => '/basic-test',
            'PHP_AUTH_USER' => $clientKey,
            'PHP_AUTH_PW' => $secretKey
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
     * Test a very simple RESTful POST request with Basic authentication.
     *
     * @group controller
     */
    public function testPostFromJson()
    {
        $bodyRaw = ['message' => 'mock me do you mocker?'];
        $clientKey = 'cb892ecb-6458-425c-9e3a-b3e99ec86f56';
        $secretKey = '4M2U1KSlv0jmqLAgs118fq4dugd534eP';

        $mock = $this->mockEnvironment([
            'PATH_INFO' => '/basic-test',
            'REQUEST_METHOD' => 'POST',
            'PHP_AUTH_USER' => $clientKey,
            'PHP_AUTH_PW' => $secretKey,
            'CONTENT_TYPE' => 'application/json',
            'slim.input' => '{"target":"mocker"}'
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
