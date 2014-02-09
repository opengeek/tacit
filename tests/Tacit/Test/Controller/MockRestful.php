<?php
/*
 * This file is part of the Tacit package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tacit\Test\Controller;


use Tacit\Controller\Restful;
use Tacit\Transform\ArrayTransformer;

class MockRestful extends Restful
{
    public function get()
    {
        parent::get();

        $this->respondWithItem(['message' => 'mock me do you?'], new ArrayTransformer());
    }
}
