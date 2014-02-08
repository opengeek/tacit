<?php
/*
 * This file is part of the Tacit package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tacit\Test\Model;


use Tacit\TestCase;

/**
 * Base test cases for Tacit\Model.
 *
 * @package Tacit\Test\Model
 */
abstract class ModelTestCase extends TestCase
{
    /**
     * A MockRepository fixture for the Model test cases.
     *
     * @var MockRepository
     */
    public $fixture;

    public function setUp()
    {
        parent::setUp();
        $this->fixture = new MockRepository();
        $this->fixture->addCollection('test');
    }
}
