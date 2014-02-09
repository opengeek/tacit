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


use Guzzle\Http\StaticClient;

class RestfulControllerTest extends ControllerTestCase
{
    /**
     * Test a very basic RESTful GET request.
     *
     * @group controller
     * @group server
     */
    public function testGet()
    {
        $this->assertEquals(array_intersect_assoc(
            ['message' => 'mock me do you?'],
            StaticClient::get($GLOBALS['service_tests_url'])->json()),
            ['message' => 'mock me do you?']
        );
    }
}
