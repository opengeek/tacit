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
 * Tests of the Tacit\Model\Persistent trait.
 *
 * @package Tacit\Test\Model
 */
class PersistentTest extends ModelTestCase
{
    protected static function fixtureData($idx = 1)
    {
        return [
            [
                'name'  => 'mock object 1',
                'date'  => new \DateTime(),
                'int'   => $idx++,
                'float' => (float)"{$idx}.{$idx}"
            ],
            [
                'name'  => 'mock object 2',
                'date'  => new \DateTime(),
                'int'   => $idx++,
                'float' => (float)"{$idx}.{$idx}"
            ],
            [
                'name'  => 'mock object 3',
                'date'  => new \DateTime(),
                'int'   => $idx++,
                'float' => (float)"{$idx}.{$idx}"
            ],
            [
                'name'  => 'mock object 4',
                'date'  => new \DateTime(),
                'int'   => $idx++,
                'float' => (float)"{$idx}.{$idx}"
            ],
            [
                'name'  => 'mock object 5',
                'date'  => new \DateTime(),
                'int'   => $idx++,
                'float' => (float)"{$idx}.{$idx}"
            ]
        ];
    }

    public function setUp()
    {
        parent::setUp();

        $collection = $this->fixture->collection('mock_persistent');

        foreach (self::fixtureData() as $item) {
            $collection->insert($item);
        }
    }

    /**
     * Test the MockPersistent::instance() method.
     */
    public function testGetMockPersistentInstance()
    {
        $this->assertInstanceOf('Tacit\\Test\\Model\\MockPersistent', MockPersistent::instance());
        $this->assertInstanceOf('Tacit\\Test\\Model\\MockPersistent', MockPersistent::instance([]));
        $this->assertInstanceOf('Tacit\\Test\\Model\\MockPersistent', MockPersistent::instance([
            'name'  => 'an instance',
            'date'  => new \DateTime(),
            'int'   => 144,
            'float' => 3.14
        ]));
    }
}
