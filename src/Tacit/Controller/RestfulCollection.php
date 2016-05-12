<?php
/*
 * This file is part of the Tacit package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tacit\Controller;


use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tacit\Controller\Exception\RestfulException;
use Tacit\Controller\Exception\ServerErrorException;
use Tacit\Controller\Exception\UnacceptableEntityException;
use Tacit\Model\Exception\ModelValidationException;
use Tacit\Model\Persistent;
use Tacit\Model\Query;
use Tacit\Operations\OperationalException;

/**
 * An abstract representation of a RESTful collection controller.
 *
 * @package Tacit\Controller
 */
abstract class RestfulCollection extends Restful
{
    protected static $allowedMethods = ['OPTIONS', 'HEAD', 'GET', 'POST'];

    /**
     * The name of a RestfulItem controller related to this RestfulCollection.
     *
     * @var string
     */
    protected static $itemController = 'Tacit\\Controller\\RestfulItem';

    protected static $transformer = 'Tacit\\Transform\\PersistentTransformer';

    /**
     * GET a representation of a pageable and sortable collection.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param array                  $args
     *
     * @return ResponseInterface|void
     * @throws RestfulException
     */
    public function get(ServerRequestInterface $request, ResponseInterface $response, array $args = [])
    {
        /** @var Persistent $modelClass */
        $modelClass = static::$modelClass;

        $criteria = $this->criteria($args, $request);

        $limit = $request->getQueryParams()['limit'] ?: 25;
        $offset = $request->getQueryParams()['offset'] ?: 0;
        $orderBy = $request->getQueryParams()['sort'] ?: $modelClass::key();
        $orderDir = $request->getQueryParams()['sort_dir'] ?: 'desc';

        try {
            $total = $modelClass::count($this->getContainer(), $criteria);

            $collection = $modelClass::find($this->getContainer(), function ($query) use ($criteria, $offset, $limit, $orderBy, $orderDir) {
                /** @var Query|object $query */
                foreach ($criteria as $criterionKey => $criterion) {
                    $query->where($criterionKey, $criterion);
                }
                $query->orderBy($orderBy, $orderDir)->skip($offset)->limit($limit);
            }, []);

            return $this->respondWithCollection($request, $response, $collection, $this->transformer(), ['total' => $total]);
        } catch (Exception $e) {
            throw new ServerErrorException('Error retrieving collection', $e->getMessage(), null, $e);
        }
    }

    /**
     * POST a new representation of an item into the collection.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param array                  $args
     *
     * @return ResponseInterface
     * @throws RestfulException
     */
    public function post(ServerRequestInterface $request, ResponseInterface $response, array $args = [])
    {
        /** @var Persistent $modelClass */
        $modelClass = static::$modelClass;

        try {
            $criteria = $this->criteria($args, $request);
            $data = $request->getParsedBody() ?: [];
            $data = !empty($criteria) ? array_replace_recursive($data, $criteria) : $data;
            $data = $this->postBeforeSet($data);
            
            $item = $modelClass::create($this->container->get('repository'), $data);

            /** @var RestfulItem $itemController */
            $itemController = static::$itemController;

            return $this->respondWithItemCreated(
                $request,
                $response,
                $item,
                $itemController::url($this->container, [$item->getKeyField() => $item->getKey()], false),
                $this->transformer()
            );
        } catch (OperationalException $e) {
            throw $e->next();
        } catch (ModelValidationException $e) {
            throw new UnacceptableEntityException('Resource validation failed', $e->getMessage(), $e->getMessages(), $e);
        } catch (Exception $e) {
            throw new ServerErrorException('Error creating item in collection', $e->getMessage(), null, $e);
        }
    }

    /**
     * Executes during POST request, before data is set to the new Item
     *
     * @param $data
     * @return array
     */
    protected function postBeforeSet($data)
    {
        return $data;
    }
}
