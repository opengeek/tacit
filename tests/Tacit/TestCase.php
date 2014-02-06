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
class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * Get a clean instance of Tacit for use in tests.
     */
    protected function setUp()
    {
        parent::setUp();
        new Tacit();
    }
}
