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

/**
 * MongaRepository tests.
 *
 * @package Tacit\Test\Model\RethinkDB
 */
class RepositoryTest extends TestCase
{
    /**
     * Test getting an instance of a Repository.
     *
     * @group model
     * @group rethinkdb
     */
    public function testGetInstance()
    {
        $this->assertInstanceOf('Tacit\Model\Repository', $this->fixture);
    }

    /**
     * Test getting a Repository connection.
     *
     * @group model
     * @group rethinkdb
     */
    public function testGetConnection()
    {
        $this->assertInstanceOf('Tacit\Model\RethinkDB\Connection', $this->fixture->getConnection());
    }

    /**
     * Test getting a Collection container wrapper.
     *
     * @group model
     * @group rethinkdb
     */
    public function testGetCollection()
    {
        $this->assertInstanceOf('Tacit\Model\RethinkDB\Collection', $this->fixture->collection('test'));
        $this->assertInstanceOf('Tacit\Model\RethinkDB\Collection', $this->fixture->collection('test', ['read_mode' => 'outdated']));
    }
}
