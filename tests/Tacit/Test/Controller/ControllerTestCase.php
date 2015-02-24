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
use Slim\Environment;
use Tacit\Tacit;
use Tacit\TestCase;

/**
 * Base test cases for RESTful Controllers.
 *
 * @package Tacit\Test\Controller
 */
abstract class ControllerTestCase extends TestCase
{
    protected function mockEnvironment(array $vars = ['REQUEST_METHOD' => 'GET'])
    {
        Environment::mock($vars);
    }

    protected function setUp()
    {
        parent::setUp();

        $this->tacit->config('tacit.identitiesFile', __DIR__ . '/../../../identities.php');
        require __DIR__ . '/../../../routes.php';
    }
}
