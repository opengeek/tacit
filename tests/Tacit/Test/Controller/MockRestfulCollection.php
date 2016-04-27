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


use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tacit\Controller\Exception\ServerErrorException;
use Tacit\Controller\RestfulCollection;
use Tacit\Model\Persistent;

class MockRestfulCollection extends RestfulCollection
{
    protected static $collectionName = 'mock_persistent';
    protected static $itemName = 'mock_persistent';
    protected static $modelClass = 'Tacit\Test\Model\MockPersistent';
    protected static $name = 'MockRestfulCollection';
    protected static $title = 'A Mock Restful Collection';
    protected static $transformer = 'Tacit\Transform\PersistentTransformer';

    public function get(ServerRequestInterface $request, ResponseInterface $response, array $args = [])
    {
        /** @var Persistent $modelClass */
        $modelClass = static::$modelClass;

        $criteria = $this->criteria($args);

        $params = $request->getQueryParams();
        $limit = isset($params['limit']) ? $params['limit'] : 25;
        $offset = isset($params['offset']) ? $params['offset'] : 0;
        $orderBy = isset($params['sort']) ? $params['sort'] : $modelClass::key();
        $orderDir = isset($params['sort_dir']) ? $params['sort_dir'] : 'desc';

        try {
            $total = $modelClass::count($criteria, $this->container->get('repository'));

            $collection = $modelClass::find($criteria, [], $this->container->get('repository'));

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

            return $this->respondWithCollection($request, $response, $collection, $this->transformer(), ['total' => $total]);
        } catch (Exception $e) {
            throw new ServerErrorException('Error retrieving collection', $e->getMessage(), null, $e);
        }
    }
}
