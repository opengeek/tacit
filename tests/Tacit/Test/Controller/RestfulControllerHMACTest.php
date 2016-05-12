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


class RestfulControllerHMACTest extends ControllerTestCase
{
    /**
     * Test a very basic RESTful GET request with Tacit HMAC authentication.
     *
     * @group controller
     */
    public function testGet()
    {
        $bodyRaw = ['message' => 'mock me do you?'];
        $clientKey = 'cb892ecb-6458-425c-9e3a-b3e99ec86f56';
        $secretKey = '4M2U1KSlv0jmqLAgs118fq4dugd534eP';
        $fingerprint = implode("\n", [
            'GET',
            md5(''),
            'application/json',
            '/hmac-test'
        ]);

        $mock = $this->mockEnvironment([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/hmac-test',
            'CONTENT_TYPE' => 'application/json',
            'Http_Content-MD5' => md5(''),
            'Http_Signature-HMAC' => dechex(time()) . ':' . $clientKey . ':' . hash_hmac('sha1', $fingerprint, $secretKey)
        ]);

        $response = $this->tacit->process($mock['request'], $mock['response']);

        $this->assertEquals(
            $bodyRaw,
            array_intersect_assoc(
                $bodyRaw,
                json_decode($response->getBody(), true)
            )
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
        $fingerprint = implode("\n", [
            'POST',
            md5('{"target":"mocker"}'),
            'application/json',
            '/hmac-test'
        ]);

        $mock = $this->mockEnvironment([
            'REQUEST_URI' => '/hmac-test',
            'REQUEST_METHOD' => 'POST',
            'CONTENT_TYPE' => 'application/json',
            'Http_Content-MD5' => md5(''),
            'Http_Signature-HMAC' => dechex(time()) . ':' . $clientKey . ':' . hash_hmac('sha1', $fingerprint, $secretKey),
            'REQUEST_BODY' => '{"target":"mocker"}'
        ]);

        $response = $this->tacit->process($mock['request'], $mock['response']);

        $this->assertEquals(
            $bodyRaw,
            array_intersect_assoc(
                $bodyRaw,
                json_decode($response->getBody(), true)
            )
        );
    }
}
