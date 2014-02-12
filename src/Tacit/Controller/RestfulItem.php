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

abstract class RestfulItem extends Restful
{
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

        $this->respondWithItem($item, $modelClass::transformer());
    }

    public function options()
    {
        /* @var \Slim\Http\Response $response */
        $response = $this->app->response;
        $response->headers->set('Content-Type', static::$responseType);
        $response->setStatus(200);
        if ($this->app->config('debug') === true) {
            $resource['request_duration'] = microtime(true) - $this->app->config('startTime');
        }
        $response->headers->set('Allow', implode(',', ['OPTIONS', 'HEAD', 'GET', 'PUT', 'PATCH', 'DELETE']));

        $this->app->stop();
    }

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

        $this->respondWithItem($item, $modelClass::transformer());
    }

    public function put()
    {
        $this->patch();
    }
}
