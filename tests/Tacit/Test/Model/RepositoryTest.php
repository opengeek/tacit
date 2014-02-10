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

/**
 * Tests of the Repository class.
 *
 * @package Tacit\Test\Model
 */
class RepositoryTest extends ModelTestCase
{
    /**
     * Test getting an instance of a Repository.
     *
     * @group model
     */
    public function testGetInstance()
    {
        $this->assertInstanceOf('Tacit\\Model\\Repository', $this->fixture);
    }

    /**
     * Test getting a Repository connection.
     *
     * @group model
     */
    public function testGetConnection()
    {
        $this->assertTrue(is_array($this->fixture->getConnection()));
    }

    /**
     * Test getting a Collection container wrapper.
     *
     * @group model
     */
    public function testGetCollection()
    {
        $this->assertInstanceOf('Tacit\\Test\\Model\\MockCollection', $this->fixture->collection('test'));
    }
}
