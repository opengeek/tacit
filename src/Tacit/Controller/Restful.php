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


use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\ResourceInterface;
use League\Fractal\TransformerAbstract;
use Slim\Route;
use Tacit\Controller\Exception\RestfulException;
use Tacit\Controller\Exception\UnauthorizedException;
use Tacit\Model\Persistent;
use Tacit\Model\Routable;
use Tacit\Tacit;
use Tacit\Transform\RestfulExceptionTransformer;

/**
 * Defines default behavior and properties for a Restful Controller.
 *
 * @package Tacit\Controller
 */
abstract class Restful
{
    const RESOURCE_TYPE_ERROR      = 0;
    const RESOURCE_TYPE_ITEM       = 1;
    const RESOURCE_TYPE_COLLECTION = 2;

    /**
     * A Resource model class represented by this Controller.
     *
     * @var string
     */
    protected static $modelClass;

    /**
     * The singular name of the Resource represented by this Controller.
     *
     * @var string
     */
    protected static $itemName = 'item';

    /**
     * The plural name of the Resource represented by this Controller.
     *
     * @var string
     */
    protected static $collectionName = 'collection';

    /**
     * The Content-Type this Controller responds with.
     *
     * @var string
     */
    protected static $responseType = 'application/json';

    /**
     * An instance of Tacit.
     *
     * @var Tacit
     */
    protected $app;

    /**
     * @var Route The route that instantiated this controller.
     */
    public $route;

    /**
     * Construct a new instance of the Restful controller.
     *
     * @param Tacit $app
     */
    public function __construct(Tacit &$app)
    {
        $this->app = $app;
        $this->route = $this->app->router->getCurrentRoute();
        $this->fractal = new Manager();
        $scopeParameter = $this->app->config('embedded_scopes_param') ? $this->app->config('embedded_scopes_param') : 'zoom';
        $this->fractal->setRequestedScopes(explode(',', $this->app->request->get($scopeParameter)));
    }

    /**
     * Handle a DELETE request.
     *
     * @return void
     */
    public function delete()
    {
        // TODO: Implement delete() method.
    }

    /**
     * Handle a GET request.
     *
     * @return void
     */
    public function get()
    {
        // TODO: Implement get() method.
    }

    /**
     * Handle a HEAD request.
     *
     * @return void
     */
    public function head()
    {
        // TODO: Implement head() method.
    }

    /**
     * Checks if the current principal is authorized for the request.
     *
     * @throws UnauthorizedException If the principal is not authorized.
     * @return bool True if authorized.
     */
    public function isAuthorized()
    {
        // TODO: Implement isAuthorized() method.
    }

    /**
     * Handle an OPTIONS request.
     *
     * @return void
     */
    public function options()
    {
        // TODO: Implement options() method.
    }

    /**
     * Handle a PATCH request.
     *
     * @return void
     */
    public function patch()
    {
        // TODO: Implement patch() method.
    }

    /**
     * Handle a POST request.
     *
     * @return void
     */
    public function post()
    {
        // TODO: Implement post() method.
    }

    /**
     * Handle a PUT request.
     *
     * @return void
     */
    public function put()
    {
        // TODO: Implement put() method.
    }

    public function respondWithError(RestfulException $exception)
    {
        $item = new Item($exception, new RestfulExceptionTransformer());
        $this->respondWithItem($item, self::RESOURCE_TYPE_ERROR, $exception->getStatus());
    }

    protected function encode($body, $options = 0)
    {
        return json_encode($body, $options);
    }

    protected function respondWithCollection($collection, TransformerAbstract $transformer)
    {
        $resource = new Collection($collection, $transformer);
        $this->respond($resource, self::RESOURCE_TYPE_COLLECTION);
    }

    protected function respondWithItem($item, TransformerAbstract $transformer)
    {
        $resource = new Item($item, $transformer);
        $this->respond($resource, self::RESOURCE_TYPE_ITEM);
    }

    /**
     * @param array|Persistent $item
     * @param TransformerAbstract $transformer
     */
    protected function respondWithItemCreated($item, TransformerAbstract $transformer)
    {
        $resource = new Item($item, $transformer);
        $this->respond(
            $resource,
            self::RESOURCE_TYPE_ITEM,
            201,
            ['headers' => [
                'Location' => $this->route(
                    ucfirst($item->getKeyField()), $item, [$item->getKeyField() => $item->getKey()]
                )
            ]]
        );
    }

    /**
     * Respond to the request with a resource.
     *
     * @param ResourceInterface $resource A fractal resource.
     * @param int   $type The type of resource to respond with.
     * @param int   $status The HTTP status code to respond with.
     * @param array $meta An optional array of metadata for the response.
     *
     * @return void
     */
    protected function respond($resource, $type = self::RESOURCE_TYPE_ITEM, $status = 200, $meta = array())
    {
        $bodyRaw = null;
        if ($resource !== null && $status !== 204) {
            $bodyRaw = array();
            $scope = $this->fractal->createData($resource)->toArray();
            switch ($type) {
                case self::RESOURCE_TYPE_ITEM:
                    foreach ($scope['data'] as $key => $value) {
                        $bodyRaw[$key] = $value;
                    }
                    break;
                case self::RESOURCE_TYPE_COLLECTION:
                    $links = array();
                    $total = $meta['total'];
                    $limit = (int)$this->app->request->get('limit', 25);
                    $offset = (int)$this->app->request->get('offset', 0);
                    if (!empty($this->route)) {
                        $links['self'] = ['href' => $this->url()];
                    }
                    if ($total > $offset) {
                        $links['first'] = ['href' => $this->url(['offset' => 0])];
                        $links['previous'] = ($offset > 0) ? ['href' => $this->url(['offset' => $offset - $limit])] : null;
                        $links['next'] = (($offset + $limit) < $total) ? ['href' => $this->url(['offset' => $offset + $limit])] : null;
                        $links['last'] = ['href' => $this->url(['offset' => (floor(($total - 1) / $limit) * $limit)])];
                    }
                    $bodyRaw['_links'] = $links;
                    $bodyRaw['_embedded'][$meta['collectionName']] = $scope['data'];
                    if ($total > $offset) {
                        $bodyRaw['total_items'] = $total;
                        $bodyRaw['returned_items'] = count($scope['data']);
                        $bodyRaw['limit'] = $limit;
                        $bodyRaw['offset'] = $offset;
                    }
                    break;
                case self::RESOURCE_TYPE_ERROR:
                    foreach ($scope['data'] as $key => $value) {
                        if ($key === 'status') {
                            $status = (int)$value;
                        }
                        $bodyRaw[$key] = $value;
                    }
                    break;
            }
        }

        /* @var \Slim\Http\Response $response */
        $response = $this->app->response;
        $response->headers->set('Content-Type', static::$responseType);
        if (isset($meta['headers']) && is_array($meta['headers'])) {
            foreach ($meta['headers'] as $headerKey => $headerValue) {
                $response->headers->set($headerKey, $headerValue);
            }
        }
        $response->setStatus($status);
        if ($bodyRaw !== null && $status !== 204) {
            if ($this->app->config('debug') === true) {
                $bodyRaw['request_duration'] = microtime(true) - $this->app->config('startTime');
            }
            $response->setBody($this->encode($bodyRaw));
        }

        $this->app->stop();
    }

    public static function route($resource, $identifier = '', array $params = array())
    {
        if (is_string($resource)) {
            $name = $resource;
        } elseif ($resource instanceof Routable) {
            $name = $resource->getRouteName();
        } else {
            $name = static::$collectionName;
        }
        $app = Tacit::getInstance();
        $url = $app->request->getUrl();
        $url .= $app->urlFor(static::$collectionName . $identifier, $params);
        return $url;
    }

    protected function url(array $params = array())
    {
        $request = $this->app->request();
        $url = $request->getUrl();
        $url .= $request->getRootUri();
        $url .= $request->getResourceUri();
        $getParams = $request->get();
        $getParams = array_merge($getParams, $params);
        if (!empty($getParams)) {
            $url .= '?';
            $qs = array();
            foreach ($getParams as $qKey => $qValue) {
                $qs[] = urlencode($qKey) . "=" . urlencode($qValue);
            }
            $url .= implode('&', $qs);
        }
        return $url;
    }
}
