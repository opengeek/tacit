<?php
/*
 * This file is part of the Tacit package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tacit\Test\Controller\RethinkDB;



use Slim\Environment;
use Tacit\Model\RethinkDB\Repository;
use Tacit\Tacit;

/**
 * Base test cases for RESTful Controllers.
 *
 * @package Tacit\Test\Controller
 */
abstract class ControllerTestCase extends \Tacit\Test\Controller\ControllerTestCase
{
    /** @var Repository */
    protected $fixture;

    protected function setUp()
    {
        $this->tacit = new Tacit([
            'app' => [
                'mode' => 'development',
                'startTime' => microtime(true)
            ],
            'connection' => [
                'class' => 'Tacit\Model\RethinkDB\Repository',
                'server' => 'localhost',
                'options' => [],
                'repository' => 'tacit_test'
            ]
        ]);

        $this->fixture = $this->tacit->getContainer()->get('repository');

        $this->tacit->config('tacit.identitiesFile', __DIR__ . '/../../../../identities.php');
        require __DIR__ . '/../../../../routes.php';

        $this->fixture->create(['exceptions' => false]);
    }

    protected function tearDown()
    {
        $this->fixture->destroy(['exceptions' => false]);
    }
}
