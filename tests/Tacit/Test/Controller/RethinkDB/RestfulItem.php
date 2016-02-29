<?php
/*
 * This file is part of the Tacit package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tacit\Test\Controller\RethinkDB;


class RestfulItem extends \Tacit\Controller\RestfulItem
{
    protected static $collectionName = 'rethinkdb_persistent';
    protected static $itemName = 'rethinkdb_persistent';
    protected static $modelClass = 'Tacit\Test\Model\RethinkDB\PersistentObject';
    protected static $name = 'RethinkDBRestfulItem';
    protected static $title = 'A RethinkDB Restful Item';
    protected static $transformer = 'Tacit\Transform\PersistentTransformer';

    public static function keys()
    {
        return ['id'];
    }
}
