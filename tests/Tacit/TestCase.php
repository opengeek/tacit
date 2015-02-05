<?php
/*
 * This file is part of the Tacit package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tacit;

/**
 * The base Tacit TestCase
 *
 * @package Tacit
 */
abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    /** @var Tacit */
    protected $tacit;

    /**
     * Get a clean instance of Tacit for use in tests.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->tacit = new Tacit([
            'app' => [
                'mode' => 'development',
                'startTime' => microtime(true)
            ],
            'connection' => [
                'class' => 'Tacit\\Test\\Model\\MockRepository',
                'server' => 'localhost',
                'options' => array('connect' => false),
                'repository' => 'tacit_test'
            ]
        ]);
    }
}
