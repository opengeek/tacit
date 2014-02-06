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
use Tacit\Model\Persistent;

class PersistentTransformer extends TransformerAbstract
{
    public function transform(Persistent $resource)
    {
        return $resource->toArray();
    }
}
