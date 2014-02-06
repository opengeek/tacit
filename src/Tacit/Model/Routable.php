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
 * Defines the behaviors and properties of a Routable model object.
 *
 * @package Tacit\Model
 */
trait Routable
{
    protected $_route;

    public function getRoute($identifier = '')
    {
        return $this->_route . (!empty($identifier) ? "_{$identifier}" : '');
    }

    public function setRoute($name)
    {
        $this->_route = $name;
    }
}
