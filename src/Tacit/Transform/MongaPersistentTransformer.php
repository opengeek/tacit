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
use Tacit\Model\Monga\MongaCollection;
use Tacit\Model\Monga\MongaPersistent;

class MongaPersistentTransformer extends TransformerAbstract
{
    public function transform(MongaPersistent $resource)
    {
        return array_merge(
            ['id' => (string)$resource->_id],
            $resource->toArray(MongaCollection::getMask($resource))
        );
    }
}
