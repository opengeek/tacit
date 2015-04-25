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


use Tacit\Controller\RestfulItem;

class MockRestfulItem extends RestfulItem
{
    protected static $collectionName = 'mock_persistent';
    protected static $itemName = 'mock_persistent';
    protected static $modelClass = 'Tacit\\Test\\Model\\MockPersistent';
    protected static $name = 'MockRestfulItem';
    protected static $title = 'A Mock Restful Item';
    protected static $transformer = 'Tacit\\Transform\\PersistentTransformer';

    public static function keys()
    {
        return ['_id'];
    }
}
