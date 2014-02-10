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
use Tacit\TestCase;

/**
 * Base test cases for RESTful Controllers.
 *
 * @package Tacit\Test\Controller
 */
abstract class ControllerTestCase extends TestCase
{
    /**
     * Make a service request to a controller path.
     *
     * @param string $path The controller path.
     * @param string $method The HTTP method to use.
     * @param array  $parameters The parameters to send.
     *
     * @return \Guzzle\Http\Message\Response|\Guzzle\Stream\Stream The response.
     */
    protected function request($path = '/', $method = 'get', array $parameters = [])
    {
        return StaticClient::request($method, $this->serviceUrl($path), $parameters);
    }

    /**
     * Get the full service tests URL appending the specified path.
     *
     * @param string $path The controller path to append.
     *
     * @return string The complete URL to the controller.
     */
    protected function serviceUrl($path)
    {
        return $GLOBALS['service_tests_url'] . '/' . ltrim($path, '/');
    }
}
