<?php
/*
 * This file is part of the Tacit package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tacit\Test\Controller\Monga;



use Slim\Environment;
use Tacit\Model\Monga\MongaRepository;
use Tacit\Tacit;

/**
 * Base test cases for RESTful Controllers.
 *
 * @package Tacit\Test\Controller
 */
abstract class ControllerTestCase extends \Tacit\Test\Controller\ControllerTestCase
{
    /** @var MongaRepository */
    protected $fixture;

    protected function mockEnvironment(array $vars = ['REQUEST_METHOD' => 'GET'])
    {
        Environment::mock($vars);
    }

    protected function setUp()
    {
        $this->tacit = new Tacit([
            'app' => [
                'mode' => 'development',
                'startTime' => microtime(true)
            ],
            'connection' => [
                'class' => 'Tacit\\Model\\Monga\\MongaRepository',
                'server' => 'localhost',
                'options' => array('connect' => false),
                'repository' => 'tacit_test'
            ]
        ]);

        $this->fixture = $this->tacit->container->get('repository');

        $this->tacit->config('tacit.identitiesFile', __DIR__ . '/../../../../identities.php');
        require __DIR__ . '/../../../../routes.php';

        $this->fixture->create(['exceptions' => false]);
    }

    protected function tearDown()
    {
        $this->fixture->destroy(['exceptions' => false]);
    }
}
