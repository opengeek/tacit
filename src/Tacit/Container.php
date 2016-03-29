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


use Tacit\Handlers\Error;

class Container extends \Slim\Container
{
    public function __construct(array $values)
    {
        $this['errorHandler'] = function(Container $c) {
            return new Error($c->get('settings')->all());
        };

        parent::__construct($values);
    }
}
