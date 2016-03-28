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
     * Test the Tacit constructor.
     *
     * @param array $config
     *
     * @dataProvider providerTacitConstructor
     */
    public function testTacitConstructor($config)
    {
        $tacit = new Tacit($config);
        $this->assertInstanceOf('Tacit\Tacit', $tacit, 'Could not get a valid instance of \Tacit\Tacit');
        $this->assertInstanceOf('Slim\App', $tacit, 'The instance of Tacit instantiated is not an instance of \Slim\App');
        $this->assertNull($tacit->config('connection'));
    }
    public function providerTacitConstructor()
    {
        return [
            [[]]
        ];
    }

    public function testRepositoryIsOptional()
    {
        $tacit = new Tacit(['connection' => null]);
        $this->assertInstanceOf('Tacit\Tacit', $tacit, 'Could not get a valid instance of \Tacit\Tacit');
        $this->assertInstanceOf('Slim\App', $tacit, 'The instance of Tacit instantiated is not an instance of \Slim\App');
        $this->assertNull($tacit->config('connection'));
        $this->assertFalse($tacit->getContainer()->has('repository'));
    }
}
