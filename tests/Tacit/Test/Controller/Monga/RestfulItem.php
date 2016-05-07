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


use MongoId;
use Psr\Http\Message\ServerRequestInterface;

class RestfulItem extends \Tacit\Controller\RestfulItem
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

    protected function criteria(array $args, ServerRequestInterface $request) {
        if (isset($args['_id'])) {
            $args['_id'] = new MongoId($args['_id']);
        }
        return parent::criteria($args, $request);
    }
}
