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


use Tacit\Controller\Exception\BadRequestException;
use Tacit\Controller\Exception\NotFoundException;
use Tacit\Controller\Exception\ServerErrorException;
use Tacit\Controller\Exception\UnacceptableEntityException;
use Tacit\Model\Exception\ModelException;

/**
 * An abstract representation of a RESTful item from a RESTful collection.
 *
 * @package Tacit\Controller
 */
abstract class RestfulItem extends Restful
{
    protected static $allowedMethods = ['OPTIONS', 'HEAD', 'GET', 'PATCH', 'PUT', 'DELETE'];

    protected static $transformer = 'Tacit\\Transform\\PersistentTransformer';

    /**
     * Delete this item from the collection.
     *
     * @throws Exception\ServerErrorException
     * @throws Exception\NotFoundException
     * @throws Exception\BadRequestException
     */
    public function delete()
    {
        /** @var \Tacit\Model\Persistent $modelClass */
        $modelClass = static::$modelClass;

        switch (func_num_args()) {
            case 0:
                throw new NotFoundException($this);
            case 1:
                $criteria = [$modelClass::key() => func_get_arg(0)];
                break;
            default:
                throw new BadRequestException($this);
        }

        /** @var \Tacit\Model\Persistent $item */
        $item = $modelClass::findOne($criteria);

        if (null === $item) {
            throw new NotFoundException($this);
        }

        try {
            $removed = $item->remove();
        } catch (\Exception $e) {
            throw new ServerErrorException($this, 'Error deleting resource.', $e->getMessage());
        }

        if (true !== $removed) {
            throw new ServerErrorException($this, 'Error deleting resource', 'The resource could not be removed.');
        }

        $this->respond(null, self::RESOURCE_TYPE_ITEM, 204);
    }

    /**
     * GET a representation of an item from the collection.
     *
     * @throws Exception\NotFoundException
     * @throws Exception\BadRequestException
     */
    public function get()
    {
        /** @var \Tacit\Model\Persistent $modelClass */
        $modelClass = static::$modelClass;

        switch (func_num_args()) {
            case 0:
                throw new NotFoundException($this);
            case 1:
                $criteria = [$modelClass::key() => func_get_arg(0)];
                break;
            default:
                throw new BadRequestException($this);
        }

        $fields = $this->app->request->get('fields', []);

        /** @var \Tacit\Model\Persistent $item */
        $item = $modelClass::findOne($criteria, $fields);

        if (null === $item) {
            throw new NotFoundException($this);
        }

        $this->respondWithItem($item, $this->transformer());
    }

    /**
     * PATCH only the properties of this item specified in the request entity.
     *
     * @throws Exception\ServerErrorException
     * @throws Exception\NotFoundException
     * @throws Exception\UnacceptableEntityException
     * @throws Exception\BadRequestException
     */
    public function patch()
    {
        /** @var \Tacit\Model\Persistent $modelClass */
        $modelClass = static::$modelClass;

        switch (func_num_args()) {
            case 0:
                throw new NotFoundException($this);
            case 1:
                $criteria = [$modelClass::key() => func_get_arg(0)];
                break;
            default:
                throw new BadRequestException($this);
        }

        /** @var \Tacit\Model\Persistent $item */
        $item = $modelClass::findOne($criteria);

        if (null === $item) {
            throw new NotFoundException($this);
        }

        try {
            $item->hydrate($this->app->request->post(null, []), (array)$modelClass::key());
            $item->save();
        } catch (ModelException $e) {
            throw new UnacceptableEntityException($this, $e->getMessage(), null, null, null, $e);
        } catch (\Exception $e) {
            throw new ServerErrorException($this, $e->getMessage(), null, null, null, $e);
        }

        $this->respondWithItem($item, $this->transformer());
    }

    /**
     * PUT the representation of this item specified in the request entity.
     *
     * NOTE: This will replace the item with the properties specified in request
     * entity. Properties not provided will be reset to default values for the item.
     */
    public function put()
    {
        /** @var \Tacit\Model\Persistent $modelClass */
        $modelClass = static::$modelClass;

        switch (func_num_args()) {
            case 0:
                throw new NotFoundException($this);
            case 1:
                $criteria = [$modelClass::key() => func_get_arg(0)];
                break;
            default:
                throw new BadRequestException($this);
        }

        /** @var \Tacit\Model\Persistent $item */
        $item = $modelClass::findOne($criteria);

        if (null === $item) {
            throw new NotFoundException($this);
        }

        try {
            /** @var \Tacit\Model\Persistent $newItem */
            $newItem = new $modelClass();
            $data = array_merge_recursive(
                $newItem->toArray(),
                $this->app->request->post(null, [])
            );
            $item->hydrate($data, (array)$modelClass::key());
            $item->save();
        } catch (ModelException $e) {
            throw new UnacceptableEntityException($this, $e->getMessage(), null, null, null, $e);
        } catch (\Exception $e) {
            throw new ServerErrorException($this, $e->getMessage(), null, null, null, $e);
        }

        $this->respondWithItem($item, $this->transformer());
    }
}
