<?php
/*
 * This file is part of the Tacit package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tacit\Model;

/**
 * An adaptor for any class providing a query interface into a collection.
 *
 * @package Tacit\Model
 */
abstract class Query
{
    /**
     * Any primitive object that provides a way to query a collection.
     *
     * NOTE: Make sure derivatives set this primitive in the constructor.
     *
     * @var object
     */
    protected $primitive;

    /**
     * Call any valid method on the primitive query class.
     *
     * @param string $name The method name.
     * @param array $arguments An array of the arguments passed to the method.
     *
     * @throws \BadMethodCallException If the method does not exist on the primitive.
     * @return mixed The return value of the primitive method.
     */
    function __call($name, $arguments)
    {
        if (method_exists($this->primitive, $name)) {
            return call_user_func_array(array($this->primitive, $name), $arguments);
        }
        throw new \BadMethodCallException(sprintf('Invalid Query method %1s invoked', [$name]));
    }
}
