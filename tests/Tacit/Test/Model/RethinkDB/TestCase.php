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

use Interop\Container\ContainerInterface;
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
        $this->tacit = new Tacit([
            'settings' => [
                'mode' => 'development',
                'startTime' => microtime(true),
                'connection' => [
                    'class' => 'Tacit\Model\RethinkDB\Repository',
                    'server' => '127.0.0.1',
                    'options' => [],
                    'repository' => 'tacit_test'
                ]
            ]
        ]);

        /** @var Repository fixture */
        $this->fixture = $this->tacit->getContainer()->get('repository');

        $this->fixture->create(['exceptions' => false]);
    }

    protected function tearDown()
    {
        $this->fixture->destroy(['exceptions' => false]);
    }
}
