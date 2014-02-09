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

use Tacit\Model\Collection;

/**
 * Tests of the Tacit\Model\Persistent trait.
 *
 * @package Tacit\Test\Model
 */
class PersistentTest extends ModelTestCase
{
    /**
     * Get fixture data starting with the specified index.
     *
     * @param int $idx The starting index value.
     *
     * @return array An array of associative arrays representing MockPersistent data.
     */
    protected static function fixtureData($idx = 1)
    {
        $data = [];
        for ($i = $idx; $i < 10; $i++) {
            $data[] = [
                'name'  => "MockPersistent #{$i}",
                'date'  => new \DateTime(),
                'int'   => $i,
                'float' => (float)"{$i}.{$i}"
            ];
        }
        return $data;
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

    /**
     * Test the MockPersistent::collection() method.
     */
    public function testCollection()
    {
        $collection = MockPersistent::collection();
        $this->assertTrue($collection instanceof Collection);
        $this->assertInstanceOf('Tacit\\Test\\Model\\MockCollection', MockPersistent::collection());
    }
}
