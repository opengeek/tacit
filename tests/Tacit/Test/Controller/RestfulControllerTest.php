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
        $this->mockEnvironment([
            'PATH_INFO' => '/'
        ]);

        $response = $this->tacit->invoke();

        $this->assertEquals(
            array_intersect_assoc(
                ['message' => 'mock me do you?'],
                json_decode($response->getBody(), true)
            ),
            ['message' => 'mock me do you?']
        );
    }
}
