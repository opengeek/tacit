<?php
/*
 * This file is part of the Tacit package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tacit\Test\Controller\Monga;


use Tacit\Controller\RestfulItem;

class MongaRestfulItem extends RestfulItem
{
    protected static $collectionName = 'monga_persistent';
    protected static $itemName = 'monga_persistent';
    protected static $modelClass = 'Tacit\Test\Model\Monga\MongaPersistentObject';
    protected static $name = 'MongaRestfulItem';
    protected static $title = 'A Monga Restful Item';
    protected static $transformer = 'Tacit\Transform\MongaPersistentTransformer';

    public static function keys()
    {
        return ['_id'];
    }

    protected function criteria(array $args) {
        $keys = static::keys();
        if (($idx = array_search('_id', $keys)) !== false) {
            $args[$idx] = new \MongoId($args[$idx]);
        }
        return parent::criteria($args);
    }
}
