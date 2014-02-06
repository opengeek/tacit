<?php
/*
 * This file is part of the Tacit package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tacit\Transform;

use League\Fractal\TransformerAbstract;

class ArrayTransformer extends TransformerAbstract
{
    public function transform(array $resource)
    {
        return $resource;
    }
}
