<?php
/*
 * This file is part of the Tacit package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tacit\Test\Validate;


use Tacit\Validate\Validator;

/**
 * Test the Tacit\Validate\Rules class.
 *
 * @package Tacit\Test\Validate
 */
class RulesTest extends ValidateTestCase
{
    /**
     * Test the classof Rule.
     *
     * @param array|object $input
     * @param array $ruleSet
     * @param bool $expected
     * @dataProvider providerClassof
     */
    public function testClassof($input, $ruleSet, $expected)
    {
        $this->assertEquals($expected, $this->validate($input, $ruleSet));
    }

    /**
     * Data provider for testClassof.
     *
     * @return array
     */
    public function providerClassof()
    {
        return [
            [['foo' => new \stdClass()], ['foo' => 'classof:\\stdClass'], true],
        ];
    }

    /**
     * Get a Validator and check an input against a set of Rules.
     *
     * @param array|object $input Input data to validate.
     * @param array        $ruleSet A set of Rules to validate the data against.
     * @param bool         $all Set to false if Rules for missing input fields should be ignored.
     *
     * @return bool Returns true if all Rules pass on the input data; false otherwise.
     */
    protected function validate($input, $ruleSet = [], $all = false)
    {
        $validator = Validator::instance($ruleSet);
        return $validator->check($input, $all);
    }
}
