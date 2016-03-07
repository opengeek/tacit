<?php
/*
 * This file is part of the Tacit package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tacit\Test\Model\RethinkDB;

use Tacit\Model\RethinkDB\Repository;
use Tacit\Tacit;

/**
 * Base test case for RethinkDB models.
 *
 * @package Tacit\Test\Model\RethinkDB
 */
abstract class TestCase extends \Tacit\TestCase
{
    /** @var Repository */
    protected $fixture;

    protected function setUp()
    {
        $tacit = new Tacit([
            'app' => [
                'mode' => 'development',
                'startTime' => microtime(true)
            ],
            'connection' => [
                'class' => 'Tacit\Model\RethinkDB\Repository',
                'server' => '127.0.0.1',
                'options' => [],
                'repository' => 'tacit_test'
            ]
        ]);
        $tacit->setName('test_rethinkdb');

        /** @var Repository fixture */
        $this->fixture = $tacit->container->get('repository');

        $this->fixture->create(['exceptions' => false]);
    }

    protected function tearDown()
    {
        $this->fixture->destroy(['exceptions' => false]);
    }
}
