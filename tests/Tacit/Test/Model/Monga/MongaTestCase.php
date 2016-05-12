<?php
/*
 * This file is part of the Tacit package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tacit\Test\Model\Monga;


use Interop\Container\ContainerInterface;
use Tacit\Model\Monga\MongaRepository;
use Tacit\Tacit;
use Tacit\TestCase;

/**
 * Base test case for Monga models (MongoDB).
 *
 * @package Tacit\Test\Model\Monga
 */
abstract class MongaTestCase extends TestCase
{
    /** @var ContainerInterface */
    protected $container;
    /** @var MongaRepository */
    protected $fixture;

    public function setUp()
    {
        $tacit = new Tacit([
            'app' => [
                'mode' => 'development',
                'startTime' => microtime(true)
            ],
            'connection' => [
                'class' => 'Tacit\Model\Monga\MongaRepository',
                'server' => 'localhost',
                'options' => array('connect' => false),
                'repository' => 'tacit_test'
            ]
        ]);

        $this->container = $tacit->getContainer();

        $this->fixture = $tacit->getContainer()->get('repository');

        $this->fixture->create(['exceptions' => false]);
    }

    protected function tearDown()
    {
        $this->fixture->destroy(['exceptions' => false]);
    }
}
