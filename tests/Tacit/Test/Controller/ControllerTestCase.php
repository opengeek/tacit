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



use Slim\Http\Body;
use Slim\Http\Environment;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Uri;
use Tacit\TestCase;

/**
 * Base test cases for RESTful Controllers.
 *
 * @package Tacit\Test\Controller
 */
abstract class ControllerTestCase extends TestCase
{
    /**
     * @param array $vars
     *
     * @return array
     */
    protected function mockEnvironment(array $vars = ['REQUEST_METHOD' => 'GET'])
    {
        $env = Environment::mock($vars);
        $uri = Uri::createFromEnvironment($env);
        $headers = Headers::createFromEnvironment($env);
        $cookies = [];
        $serverParams = $env->all();
        $body = new Body(fopen('php://temp', 'r+'));
        $req = new Request('POST', $uri, $headers, $cookies, $serverParams, $body);
        $res = new Response();

        return ['request' => $req, 'response' => $res];
    }

    protected function setUp()
    {
        parent::setUp();

        $this->tacit->config('tacit.identitiesFile', __DIR__ . '/../../../identities.php');
        require __DIR__ . '/../../../routes.php';
    }
}
