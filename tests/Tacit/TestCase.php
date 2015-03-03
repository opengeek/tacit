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
abstract class TestCase extends \PHPUnit_Framework_TestCase
{
    /** @var Tacit */
    protected $tacit;

    /**
     * Recursively compare multi-dimensional associative array items.
     *
     * @param mixed $val1
     * @param mixed $val2
     *
     * @return int 0 if equal, non-zero otherwise
     */
    public function compareMultidimensionalArray($val1, $val2)
    {
        if (is_array($val1) && is_array($val2)) {
            $arr = array_uintersect_assoc($val1, $val2, array($this, 'compareMultidimensionalArray'));
            if (count($arr) == max(count($val1), count($val2))) {
                return 0;
            }

            return -1;
        }

        return strcmp($val1, $val2);
    }

    /**
     * Get a clean instance of Tacit for use in tests.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->tacit = new Tacit([
            'app' => [
                'mode' => 'development',
                'startTime' => microtime(true)
            ],
            'connection' => [
                'class' => 'Tacit\\Test\\Model\\MockRepository',
                'server' => 'localhost',
                'options' => array('connect' => false),
                'repository' => 'tacit_test'
            ]
        ]);
    }
}
