<?php
/*
 * This file is part of the Tacit package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tacit\Test;


use Tacit\Tacit;
use Tacit\TestCase;

/**
 * Tests for the Tacit class.
 *
 * @package Tacit\Test
 */
class TacitTest extends TestCase
{
    /**
     * Test Tacit::getInstance()
     */
    public function testGetInstance()
    {
        $tacit = Tacit::getInstance();
        $this->assertInstanceOf('Tacit\\Tacit', $tacit, 'Could not get a valid instance of \\Tacit\\Tacit');
        $this->assertInstanceOf('Slim\\Slim', $tacit, 'The instance of Tacit instantiated is not an instance of \\Slim\\Slim');
    }

    /**
     * Test the Tacit constructor.
     *
     * @param array $config
     *
     * @dataProvider providerTacitConstructor
     */
    public function testTacitConstructor($config)
    {
        $tacit = new Tacit($config);
        $this->assertInstanceOf('Tacit\\Tacit', $tacit, 'Could not get a valid instance of \\Tacit\\Tacit');
        $this->assertInstanceOf('Slim\\Slim', $tacit, 'The instance of Tacit instantiated is not an instance of \\Slim\\Slim');
        $this->assertEquals(
            [
                'class' => 'Tacit\\Model\\Monga\\MongaRepository',
                'server' => 'mongodb://localhost',
                'options' => array('connect' => false),
                'repository' => 'test'
            ],
            $tacit->config('connection')
        );
    }
    public function providerTacitConstructor()
    {
        return [
            [[]]
        ];
    }
}
