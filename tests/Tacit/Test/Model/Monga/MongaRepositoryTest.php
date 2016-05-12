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

/**
 * MongaRepository tests.
 *
 * @package Tacit\Test\Model\Monga
 */
class MongaRepositoryTest extends MongaTestCase
{
    /**
     * Test getting an instance of a Repository.
     *
     * @group model
     * @group monga
     */
    public function testGetInstance()
    {
        $this->assertInstanceOf('Tacit\Model\Repository', $this->fixture);
    }

    /**
     * Test getting a Repository connection.
     *
     * @group model
     * @group monga
     */
    public function testGetConnection()
    {
        $this->assertInstanceOf('League\Monga\Database', $this->fixture->getConnection());
    }

    /**
     * Test getting a Collection container wrapper.
     *
     * @group model
     * @group monga
     */
    public function testGetCollection()
    {
        $this->assertInstanceOf('Tacit\Model\Monga\MongaCollection', $this->fixture->collection('test'));
    }
}
