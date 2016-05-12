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

use Slim\App;

/**
 * A wrapper for Slim to provide RESTful RAD server features.
 *
 * @package Tacit
 */
class Tacit extends App
{
    /**
     * Construct a new instance of Tacit.
     *
     * @param array $container
     */
    public function __construct($container = [])
    {
        if (is_array($container)) {
            $container = new Container($container);
        }

        parent::__construct($container);
    }
}
