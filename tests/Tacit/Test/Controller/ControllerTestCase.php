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
    protected $environment = [];

    protected function mockEnvironment(array $vars = [])
    {
        $this->environment = [
            'REQUEST_METHOD' => 'GET',
            'CONTENT_TYPE' => 'application/json',
            'slim.input' => '',
        ];

        $this->environment = array_merge_recursive($this->environment, $vars);

        Environment::mock($this->environment);
    }

    protected function setUp()
    {
        parent::setUp();

        $this->tacit->config('tacit.identitiesFile', __DIR__ . '/../../../identities.php');
        require __DIR__ . '/../../../routes.php';
    }
}
