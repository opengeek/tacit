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
use Tacit\Controller\Exception\ServerErrorException;
use Tacit\Controller\Exception\UnacceptableEntityException;
use Tacit\Model\Exception\ModelValidationException;
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
     * @throws ServerErrorException
     */
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

            $collection = $modelClass::find(function ($query) use ($criteria, $offset, $limit, $orderBy, $orderDir) {
                /** @var Query|object $query */
                foreach ($criteria as $criterionKey => $criterion) {
                    $query->where($criterionKey, $criterion);
                }
                $query->orderBy($orderBy, $orderDir)->skip($offset)->limit($limit);
            }, [], $this->app->container->get('repository'));
        } catch (Exception $e) {
            throw new ServerErrorException($this, 'Error retrieving collection', $e->getMessage(), null, $e);
        }

        $this->respondWithCollection($collection, $this->transformer(), ['total' => $total]);
    }

    /**
     * POST a new representation of an item into the collection.
     *
     * @throws ServerErrorException
     * @throws UnacceptableEntityException
     */
    public function post()
    {
        /** @var \Tacit\Model\Persistent $modelClass */
        $modelClass = static::$modelClass;

        try {
            $criteria = $this->criteria(func_get_args());
            $data = !empty($criteria) ? array_replace_recursive($this->app->request->post(null, []), $criteria) : $this->app->request->post(null, []);
            $data = $this->postBeforeSet($data);
            
            $item = $modelClass::create($data, $this->app->container->get('repository'));
        } catch (OperationalException $e) {
            $e->next($this);
        } catch (ModelValidationException $e) {
            throw new UnacceptableEntityException($this, 'Resource validation failed', $e->getMessage(), $e->getMessages(), $e);
        } catch (Exception $e) {
            throw new ServerErrorException($this, 'Error creating item in collection', $e->getMessage(), null, $e);
        }

        /** @var RestfulItem $itemController */
        $itemController = static::$itemController;

        $this->respondWithItemCreated(
            $item,
            $itemController::url($this->app, [$item->getKeyField() => $item->getKey()], false),
            $this->transformer()
        );
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
