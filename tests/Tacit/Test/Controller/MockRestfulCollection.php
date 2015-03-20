<?php
/*
 * This file is part of the Tacit package.
 *
 * Copyright (c) MODX, LLC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tacit\Test\Controller;


use Tacit\Controller\Exception\ServerErrorException;
use Tacit\Controller\RestfulCollection;

class MockRestfulCollection extends RestfulCollection
{
    protected static $collectionName = 'mock_persistent';
    protected static $itemName = 'mock_persistent';
    protected static $modelClass = 'Tacit\\Test\\Model\\MockPersistent';
    protected static $name = 'MockRestfulCollection';
    protected static $title = 'A Mock Restful Collection';
    protected static $transformer = 'Tacit\\Transform\\PersistentTransformer';

    public function get()
    {
        /** @var \Tacit\Model\Persistent $modelClass */
        $modelClass = static::$modelClass;

        $criteria = $this->criteria(func_get_args());

        $limit = $this->app->request->get('limit', 25);
        $offset = $this->app->request->get('offset', 0);
        $orderBy = $this->app->request->get('sort', $modelClass::key());
        $orderDir = $this->app->request->get('sort_dir', 'desc');

        try {
            $total = $modelClass::count($criteria, $this->app->container->get('repository'));

            $collection = $modelClass::find($criteria, [], $this->app->container->get('repository'));

            if ($collection === null) {
                $collection = [];
            }
            if ($collection && $orderBy) {
                usort($collection, function($a, $b) use ($orderBy, $orderDir) {
                    switch ($orderDir) {
                        case 'desc':
                        case 'DESC':
                        case -1:
                            return strnatcmp($b->{$orderBy}, $a->{$orderBy});
                        default:
                            return strnatcmp($a->{$orderBy}, $b->{$orderBy});
                    }
                });
            }

            if ($collection && $limit > 0) {
                $collection = array_slice($collection, $offset, $limit);
            }
        } catch (\Exception $e) {
            throw new ServerErrorException($this, 'Error retrieving collection', $e->getMessage(), null, $e);
        }

        $this->respondWithCollection($collection, $this->transformer(), ['total' => $total]);
    }
}
