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
use Tacit\Model\Repository;
use Tacit\TacitTestCase;

/**
 * Base test cases for RESTful Controllers.
 *
 * @package Tacit\Test\Controller
 */
abstract class ControllerTestCase extends TacitTestCase
{
    /**
     * A MockRepository fixture for the Model test cases.
     *
     * @var Repository
     */
    public $fixture;

    /**
     * @param array $vars
     *
     * @return array
     */
    protected function mockEnvironment(array $vars = [])
    {
        $vars = array_replace(['REQUEST_METHOD' => 'GET'], $vars);

        $env = Environment::mock($vars);
        $uri = Uri::createFromEnvironment($env);
        $headers = Headers::createFromEnvironment($env);
        $cookies = [];
        $serverParams = $env->all();
        $body = new Body(fopen('php://temp', 'r+'));
        if (isset($vars['REQUEST_BODY'])) {
            $body->write($vars['REQUEST_BODY']);
        } elseif (isset($vars['slim.input'])) {
            $body->write($vars['slim.input']);
        }
        $req = new Request($vars['REQUEST_METHOD'], $uri, $headers, $cookies, $serverParams, $body);
        $res = new Response();

        return ['request' => $req, 'response' => $res];
    }

    protected function setUp()
    {
        parent::setUp();

        $this->fixture = $this->tacit->getContainer()->get('repository');

        $this->tacit->getContainer()->get('settings')->set('tacit.identitiesFile', __DIR__ . '/../../../identities.php');
        require __DIR__ . '/../../../routes.php';
    }
}
