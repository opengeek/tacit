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


use Tacit\Controller\Exception\ServerErrorException;
use Tacit\Controller\Exception\UnacceptableEntityException;
use Tacit\Model\Exception\ModelValidationException;
use Tacit\Model\Query;

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
     * @throws Exception\ServerErrorException
     */
    public function get()
    {
        $limit = $this->app->request->get('limit', 25);
        $offset = $this->app->request->get('offset', 0);
        $orderBy = $this->app->request->get('sort', 'created_at');
        $orderDir = $this->app->request->get('sort_dir', 'desc');

        /** @var \Tacit\Model\Persistent $modelClass */
        $modelClass = static::$modelClass;

        try {
            $total = $modelClass::count();

            $collection = $modelClass::find(function ($query) use ($offset, $limit, $orderBy, $orderDir) {
                /** @var Query|object $query */
                $query->orderBy($orderBy, $orderDir)->skip($offset)->limit($limit);
            });
        } catch (\Exception $e) {
            throw new ServerErrorException($this, 'Error retrieving collection', $e->getMessage(), null, $e);
        }

        $this->respondWithCollection($collection, $this->transformer(), ['total' => $total]);
    }

    /**
     * POST a new representation of an item into the collection.
     *
     * @throws Exception\ServerErrorException
     * @throws Exception\UnacceptableEntityException
     */
    public function post()
    {
        /** @var \Tacit\Model\Persistent $modelClass */
        $modelClass = static::$modelClass;

        try {
            $item = $modelClass::create($this->app->request->post(null, []));
        } catch (ModelValidationException $e) {
            throw new UnacceptableEntityException($this, 'Resource validation failed', $e->getMessage(), $e->getMessages(), $e);
        } catch (\Exception $e) {
            throw new ServerErrorException($this, 'Error creating item in collection', $e->getMessage(), null, $e);
        }

        $this->respondWithItemCreated($item, static::$itemController, $this->transformer());
    }
}
