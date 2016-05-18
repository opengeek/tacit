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
use League\Fractal\Manager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Router;
use Tacit\Controller\Exception\NotFoundException;
use Tacit\Controller\Exception\RestfulException;
use Tacit\Controller\Exception\ServerErrorException;
use Tacit\Controller\Exception\UnacceptableEntityException;
use Tacit\Model\Collection;
use Tacit\Model\Exception\ModelValidationException;
use Tacit\Model\Repository;
use Tacit\Model\RethinkDB\Persistent;
use Tacit\Operations\OperationalException;

/**
 * An abstract representation of a RESTful item from a RESTful collection.
 *
 * @package Tacit\Controller
 */
abstract class RestfulItem extends Restful
{
    protected static $allowedMethods = ['OPTIONS', 'HEAD', 'GET', 'PATCH', 'PUT', 'DELETE'];

    protected static $transformer = 'Tacit\Transform\PersistentTransformer';

    /**
     * Get a model Repository instance for this controller.
     *
     * @var Repository
     */
    protected $repository;

    /**
     * Construct a new instance of the Restful controller.
     *
     * @param \Slim\Collection $settings
     * @param Router           $router
     * @param Manager          $fractal
     * @param Repository       $repository
     */
    public function __construct(\Slim\Collection $settings, Router $router, Manager $fractal, Repository $repository)
    {
        parent::__construct($settings, $router, $fractal);
        $this->repository = $repository;
    }

    /**
     * Delete this item from the collection.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param array                  $args
     *
     * @return ResponseInterface
     * @throws RestfulException
     */
    public function delete(ServerRequestInterface $request, ResponseInterface $response, array $args = [])
    {
        /** @var Persistent $modelClass */
        $modelClass = static::$modelClass;

        /** @var Persistent $item */
        $item = $modelClass::findOne($this->repository, $this->criteria($args, $request), []);

        if (null === $item) {
            throw new NotFoundException();
        }

        try {
            $removed = $item->remove();
        } catch (Exception $e) {
            throw new ServerErrorException('Error deleting resource.', $e->getMessage());
        }

        if (true !== $removed) {
            throw new ServerErrorException('Error deleting resource', 'The resource could not be removed.');
        }

        return $this->respond($request, $response, null, self::RESOURCE_TYPE_ITEM, 204);
    }

    /**
     * GET a representation of an item from the collection.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param array                  $args
     *
     * @return ResponseInterface
     * @throws RestfulException
     */
    public function get(ServerRequestInterface $request, ResponseInterface $response, array $args = [])
    {
        /** @var Persistent $modelClass */
        $modelClass = static::$modelClass;

        $criteria = $this->criteria($args, $request);


        $fields = isset($request->getQueryParams()['fields']) ? $request->getQueryParams()['fields'] : '';
        $fields = array_filter(explode(',', $fields));

        /** @var Persistent $item */
        $item = $modelClass::findOne($this->repository, $criteria, $fields);

        if (null === $item) {
            throw new NotFoundException();
        }

        return $this->respondWithItem($request, $response, $item, new static::$transformer());
    }

    /**
     * PATCH only the properties of this item specified in the request entity.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param array                  $args
     *
     * @return ResponseInterface
     * @throws RestfulException
     */
    public function patch(ServerRequestInterface $request, ResponseInterface $response, array $args = [])
    {
        /** @var Persistent $modelClass */
        $modelClass = static::$modelClass;

        $criteria = $this->criteria($args, $request);

        /** @var Persistent $item */
        $item = $modelClass::findOne($this->repository, $criteria, []);

        if (null === $item) {
            throw new NotFoundException();
        }

        try {
            $data = $request->getParsedBody();
            $data = $this->patchBeforeSet($data);
            
            $item->fromArray($data, Collection::getMask($item));
            $item->save();

            return $this->respondWithItem($request, $response, $item, new static::$transformer());
        } catch (OperationalException $e) {
            throw $e->next();
        } catch (ModelValidationException $e) {
            throw new UnacceptableEntityException('Resource validation failed', $e->getMessage(), $e->getMessages(), $e);
        } catch (Exception $e) {
            throw new ServerErrorException('Error patching resource', $e->getMessage(), null, $e);
        }
    }

    /**
     * PUT the representation of this item specified in the request entity.
     *
     * NOTE: This will replace the item with the properties specified in request
     * entity. Properties not provided will be reset to default values for the item.
     * 
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param array                  $args
     *
     * @return ResponseInterface
     * @throws RestfulException
     */
    public function put(ServerRequestInterface $request, ResponseInterface $response, array $args = [])
    {
        /** @var Persistent $modelClass */
        $modelClass = static::$modelClass;

        $criteria = $this->criteria($args, $request);

        /** @var Persistent $item */
        $item = $modelClass::findOne($this->repository, $criteria, []);

        if (null === $item) {
            throw new NotFoundException();
        }

        try {
            /** @var Persistent $newItem */
            $newItem = new $modelClass($this->repository);
            $data = array_replace_recursive(
                $newItem->toArray(),
                $request->getParsedBody() ?: []
            );
            
            $data = $this->putBeforeSet($data);
            
            $item->fromArray($data, Collection::getMask($item));
            $item->save();

            return $this->respondWithItem($request, $response, $item, new static::$transformer());
        } catch (OperationalException $e) {
            throw $e->next();
        } catch (ModelValidationException $e) {
            throw new UnacceptableEntityException('Resource validation failed', $e->getMessage(), $e->getMessages(), $e);
        } catch (Exception $e) {
            throw new ServerErrorException('Error updating resource', $e->getMessage(), null, $e);
        }
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
