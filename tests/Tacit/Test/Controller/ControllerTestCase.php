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
    protected $environment = [
        'REQUEST_METHOD' => 'GET',
        'CONTENT_TYPE' => 'application/json',
        'slim.input' => '',
    ];

    protected function mockEnvironment(array $vars = [], $merge = true)
    {
        $this->environment = $merge
            ? array_merge_recursive($this->environment, $vars)
            : $vars;
        Environment::mock($this->environment);
        return $this->environment;
    }

    protected function setUp()
    {
        parent::setUp();

        $tacit = Tacit::getInstance();
        $tacit->config('tacit.identitiesFile', __DIR__ . '/../../../identities.php');
        require __DIR__ . '/../../../routes.php';
    }
}
