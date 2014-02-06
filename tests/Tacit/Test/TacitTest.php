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

class TacitTest extends TestCase
{
    public function testGetInstance()
    {
        $tacit = Tacit::getInstance();
        $this->assertInstanceOf('Tacit\\Tacit', $tacit, 'Could not get a valid instance of \\Tacit\\Tacit');
        $this->assertInstanceOf('Slim\\Slim', $tacit, 'The instance of Tacit instantiated is not an instance of \\Slim\\Slim');
    }
} 
