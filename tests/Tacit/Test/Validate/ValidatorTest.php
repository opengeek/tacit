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
 * Test the Tacit\Validate\Validator class.
 *
 * @package Tacit\Test\Validate
 */
class ValidatorTest extends ValidateTestCase
{
    /**
     * Test the Validator::instance() method.
     *
     * @group validate
     */
    public function testInstance()
    {
        $this->assertInstanceOf('Tacit\\Validate\\Validator', Validator::instance());
    }
}
