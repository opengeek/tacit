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


use Tacit\Controller\Exception\NotFoundException;
use Tacit\Controller\Exception\ServerErrorException;
use Tacit\Controller\Exception\UnacceptableEntityException;
use Tacit\Model\Collection;
use Tacit\Model\Exception\ModelValidationException;
use Tacit\Operations\OperationalException;

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

        /** @var \Tacit\Model\Persistent $item */
        $item = $modelClass::findOne($this->criteria(func_get_args()), [], $this->app->container->get('repository'));

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

        $criteria = $this->criteria(func_get_args());

        $fields = array_filter(explode(',', $this->app->request->get('fields', '')));

        /** @var \Tacit\Model\Persistent $item */
        $item = $modelClass::findOne($criteria, $fields, $this->app->container->get('repository'));

        if (null === $item) {
            throw new NotFoundException($this);
        }

        $this->respondWithItem($item, new static::$transformer());
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

        $criteria = $this->criteria(func_get_args());

        /** @var \Tacit\Model\Persistent $item */
        $item = $modelClass::findOne($criteria, [], $this->app->container->get('repository'));

        if (null === $item) {
            throw new NotFoundException($this);
        }

        try {
            $data = $this->app->request->post(null, []);
            $data = $this->patchBeforeSet($data);
            
            $item->fromArray($data, Collection::getMask($item));
            $item->save();
        } catch (OperationalException $e) {
            $e->next($this);
        } catch (ModelValidationException $e) {
            throw new UnacceptableEntityException($this, 'Resource validation failed', $e->getMessage(), $e->getMessages(), $e);
        } catch (\Exception $e) {
            throw new ServerErrorException($this, 'Error patching resource', $e->getMessage(), null, $e);
        }

        $this->respondWithItem($item, new static::$transformer());
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

        $criteria = $this->criteria(func_get_args());

        /** @var \Tacit\Model\Persistent $item */
        $item = $modelClass::findOne($criteria, [], $this->app->container->get('repository'));

        if (null === $item) {
            throw new NotFoundException($this);
        }

        try {
            /** @var \Tacit\Model\Persistent $newItem */
            $newItem = new $modelClass();
            $data = array_replace_recursive(
                array_filter($newItem->toArray(), [$this, 'filterNull']),
                $this->app->request->post(null, [])
            );
            
            $data = $this->putBeforeSet($data);
            
            $item->fromArray($data, Collection::getMask($item));
            $item->save();
        } catch (OperationalException $e) {
            $e->next($this);
        } catch (ModelValidationException $e) {
            throw new UnacceptableEntityException($this, 'Resource validation failed', $e->getMessage(), $e->getMessages(), $e);
        } catch (\Exception $e) {
            throw new ServerErrorException($this, 'Error updating resource', $e->getMessage(), null, $e);
        }

        $this->respondWithItem($item, new static::$transformer());
    }

    /**
     * Executes during PUT request, before data is set to the Item
     *
     * @param $data
     * @return array
     */
    protected function putBeforeSet($data)
    {
        return $data;
    }

    /**
     * Executes during PATCH request, before data is set to the Item
     *
     * @param $data
     * @return array
     */
    protected function patchBeforeSet($data)
    {
        return $data;
    }

    protected function filterNull($item)
    {
        if (is_array($item)) {
            return array_filter($item, [$this, 'filterNull']);
        }

        if ($item === null) {
            return false;
        }
        return true;
    }
}
