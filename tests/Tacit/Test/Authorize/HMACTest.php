<?php
/*
 * This file is part of the Tacit package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tacit\Test\Authorize;


use Tacit\Authorize\HMAC;
use Tacit\Tacit;
use Tacit\TestCase;

class HMACTest extends TestCase
{
    /**
     * Test the HMAC->__construct()
     */
    public function testConstructor()
    {
        $hmac = new HMAC();
        $this->assertInstanceOf('Tacit\\Authorize\\HMAC', $hmac);
        $this->assertInstanceOf('Tacit\\Authorize\\Authorization', $hmac);
    }

    /**
     * Test the static HMAC::identities() method.
     *
     * @param array $expected
     *
     * @dataProvider providerIdentities
     */
    public function testIdentities($expected)
    {
        $this->tacit->config('tacit.identitiesFile', __DIR__ . '/../../../identities.php');
        $this->assertEquals($expected, (new HMAC())->identities($this->tacit));
    }
    /**
     * Provider for testIdentities()
     *
     * @return array
     */
    public function providerIdentities()
    {
        return [
            [
                [
                    'cb892ecb-6458-425c-9e3a-b3e99ec86f56' => [
                        'secretKey' => '4M2U1KSlv0jmqLAgs118fq4dugd534eP',
                        'identity' => 'Tacit Identity Test',
                    ]
                ]
            ]
        ];
    }
}
