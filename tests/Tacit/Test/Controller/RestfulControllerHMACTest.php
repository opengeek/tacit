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

class RestfulControllerHMACTest extends ControllerTestCase
{
    /**
     * Test a very basic RESTful GET request.
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
            '',
            '/hmac-test'
        ]);

        $this->mockEnvironment([
            'PATH_INFO' => '/hmac-test',
            'Http_Content-MD5' => md5(''),
            'Http_Signature-HMAC' => dechex(time()) . ':' . $clientKey . ':' . hash_hmac('sha1', $fingerprint, $secretKey)
        ]);

        /** @var Tacit $app */
        $app = Tacit::getInstance();

        $response = $app->invoke();

        $this->assertEquals(
            $bodyRaw,
            json_decode($response->getBody(), true)
        );
    }
}
