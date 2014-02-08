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


class RepositoryTest extends ModelTestCase
{
    /** @var MockRepository */
    public $fixture;

    public function setUp()
    {
        parent::setUp();
        $this->fixture = new MockRepository();
        $this->fixture->addCollection('test');
    }

    public function testGetInstance()
    {
        $this->assertInstanceOf('Tacit\\Model\\Repository', $this->fixture);
    }

    public function testGetConnection()
    {
        $this->assertInstanceOf('stdClass', $this->fixture->getConnection());
    }

    public function testGetCollection()
    {
        $this->assertInstanceOf('Tacit\\Test\\Model\\MockCollection', $this->fixture->collection('test'));
    }
}
