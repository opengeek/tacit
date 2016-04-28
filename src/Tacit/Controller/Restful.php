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
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;
use Slim\Http\Stream;
use Slim\Route;
use Slim\Router;
use Tacit\Container;
use Tacit\Controller\Exception\BadRequestException;
use Tacit\Controller\Exception\MethodNotAllowedException;
use Tacit\Controller\Exception\NotImplementedException;
use Tacit\Controller\Exception\RestfulException;
use Tacit\Controller\Exception\UnauthorizedException;
use Tacit\Model\Persistent;
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
     * An array of allowed HTTP methods for the controller.
     *
     * @var array[string]
     */
    protected static $allowedMethods = ['OPTIONS', 'HEAD', 'GET', 'POST', 'PUT', 'PATCH', 'DELETE'];

    /**
     * The plural name of the Resource represented by this Controller.
     *
     * @var string
     */
    protected static $collectionName = 'collection';

    /**
     * The singular name of the Resource represented by this Controller.
     *
     * @var string
     */
    protected static $itemName = 'item';

    /**
     * A Resource model class represented by this Controller.
     *
     * @var string
     */
    protected static $modelClass;

    /**
     * The route name of the controller
     *
     * @var string
     */
    protected static $name;

    /**
     * The Content-Type this Controller responds with.
     *
     * @var string
     */
    protected static $responseType = 'application/json';

    /**
     * The title of the controller.
     *
     * @var string
     */
    protected static $title;

    /**
     * The default Transformer for this controller.
     *
     * @var string
     */
    protected static $transformer = '\Tacit\Transform\ArrayTransformer';

    /**
     * An array of refs for this controller.
     *
     * @var array
     */
    protected $refs = [];

    /**
     * The DI container for access to dependencies
     *
     * @var Container
     */
    protected $container;

    public static function className()
    {
        return __CLASS__;
    }

    /**
     * Get default criteria for this controller.
     *
     * @return array
     */
    public static function defaultCriteria()
    {
        return [];
    }

    /**
     * Get the route name of this Restful controller.
     *
     * @return string
     */
    public static function name()
    {
        if (!isset(static::$name)) {
            $explodedClass = explode('\\', static::className());
            static::$name = array_pop($explodedClass);
        }
        return static::$name;
    }

    /**
     * Get the required parameter keys for this controller.
     *
     * @return array[string]
     */
    public static function keys()
    {
        return [];
    }

    /**
     * Return a RESTful ref element for this controller.
     *
     * @param Container  $container
     * @param array      $routeParams
     * @param array|bool $params
     * @param string     $suffix
     *
     * @return array
     */
    public static function ref(Container $container, array $routeParams = [], $params = false, $suffix = '')
    {
        return [
            'href' => static::url($container, $routeParams, $params),
            'title' => static::title() . (!empty($suffix) ? " {$suffix}" : '')
        ];
    }

    /**
     * Get the title of this Restful controller.
     * @return string
     */
    public static function title()
    {
        return static::$title ?: static::name();
    }

    /**
     * Get the URL for this Restful controller.
     *
     * @param Container  $container
     * @param array      $routeParams
     * @param array|bool $params
     *
     * @return string
     */
    public static function url(Container $container, array $routeParams = [], $params = [])
    {
        /** @var ServerRequestInterface $request */
        $request = $container->get('request');

        /** @var Router $router */
        $router = $container->get('router');

        $uri = $request->getUri();

        $getParams = [];
        if (false !== $params && is_array($params)) {
            /** @var Route $currentRoute */
            $currentRoute = $request->getAttribute('route');

            $getParams = $currentRoute && $currentRoute->getName() !== static::name()
                ? $request->getQueryParams()
                : [];
            $getParams = array_merge($getParams, $params);
        }

        return $uri->getScheme() . '://' . $uri->getHost() . $uri->getPort() . $router->pathFor(static::name(), $routeParams, $getParams);
    }

    /**
     * Construct a new instance of the Restful controller.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->fractal = new Manager();
        $scopeParameter = isset($this->container->settings['embedded_scopes_param']) ? $this->container->settings['embedded_scopes_param'] : 'zoom';
        if (isset($this->container->request->getQueryParams()[$scopeParameter])) {
            $this->fractal->setRequestedScopes(explode(',', $this->container->request->getQueryParams()[$scopeParameter]));
        }
    }

    /**
     * Get the DI container for this controller.
     *
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Add an array of refs to this controller.
     *
     * @param array $refs An array of refs to add to this controller.
     */
    public function addRefs(array $refs = [])
    {
        $this->refs = array_merge($this->refs, $refs);
    }

    /**
     * Handle a DELETE request.
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
        throw new NotImplementedException();
    }

    /**
     * Handle a GET request.
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
        throw new NotImplementedException();
    }

    /**
     * Route to the appropriate HTTP method.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param array                  $args
     *
     * @return ResponseInterface
     * @throws RestfulException
     */
    public function handle(ServerRequestInterface $request, ResponseInterface $response, array $args = [])
    {
        $this->checkMethod($request);

        $method = $request->getMethod();
        if (method_exists($this, $method)) {
            return $this->{$method}($request, $response, $args);
        }
        throw new NotImplementedException();
    }

    /**
     * Handle a HEAD request.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param array                  $args
     *
     * @return ResponseInterface
     * @throws RestfulException
     */
    public function head(ServerRequestInterface $request, ResponseInterface $response, array $args = [])
    {
        return $this->get($request, $response, $args);
    }

    /**
     * Checks if the current principal is authorized for the request.
     *
     * @throws UnauthorizedException If the principal is not authorized.
     * @return bool True if authorized.
     */
    public function isAuthorized()
    {
        return true;
    }

    /**
     * Handle an OPTIONS request.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param array                  $args
     *
     * @return ResponseInterface
     * @throws RestfulException
     */
    public function options(ServerRequestInterface $request, ResponseInterface $response, array $args = [])
    {
        return $response->withHeader('Content-Type', static::$responseType)
            ->withHeader('Allow', implode(',', static::$allowedMethods))
            ->withStatus(200);
    }

    /**
     * Handle a PATCH request.
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
        throw new NotImplementedException();
    }

    /**
     * Handle a POST request.
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
        throw new NotImplementedException();
    }

    /**
     * Handle a PUT request.
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
        throw new NotImplementedException();
    }

    /**
     * Get an array of refs for this controller.
     *
     * @param array $refs An optional array of refs to merge with those already
     * defined for this controller.
     * @param array $parameters An array of parameters for building the self link.
     *
     * @return array An array of refs for this controller.
     */
    public function refs(array $refs = [], array $parameters = [])
    {
        return array_merge(
            [
                'self' => [
                    'href' => $this->self($parameters),
                    'title' => static::title()
                ]
            ],
            $this->refs,
            $refs
        );
    }

    /**
     * Respond with a RestfulException.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param RestfulException       $exception
     *
     * @return ResponseInterface
     */
    public function respondWithError(ServerRequestInterface $request, ResponseInterface $response, RestfulException $exception)
    {
        $item = new Item($exception, new RestfulExceptionTransformer());

        return $this->respond($request, $response, $item, self::RESOURCE_TYPE_ERROR, $exception->getStatus());
    }

    /**
     * Get and/or set the Transformer for this controller.
     *
     * @param null|string $transformerClass Accepts an optional class name to set
     * the current Transformer for this controller to before returning an instance.
     *
     * @return TransformerAbstract An instance of the current Transformer for this
     * controller.
     */
    public function transformer($transformerClass = null)
    {
        if (is_string($transformerClass) && class_exists($transformerClass)) {
            static::$transformer = $transformerClass;
        }

        return new static::$transformer;
    }

    /**
     * Get the criteria used to identify the resource associated with this controller.
     *
     * @param array $args
     *
     * @throws BadRequestException
     * @return array
     */
    protected function criteria(array $args)
    {
        $keys = static::keys();
        if (count($keys) !== count($args)) {
            throw new BadRequestException(null, "Wrong number of arguments for this resource", static::keys());
        }

        return array_replace_recursive(static::defaultCriteria(), $args);
    }

    /**
     * Encode an array of data representing a response body.
     *
     * @param array $body An array of data representing the response body.
     *
     * @return string The encoded response body as a string.
     */
    protected function encode($body)
    {
        return json_encode($body, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Respond with a Collection.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param array[Persistent]      $collection  A collection to transform and  respond with.
     * @param TransformerAbstract    $transformer The transformer to apply to the items in the collection.
     * @param array                  $meta        An array of metadata associated with the collection.
     *
     * @return ResponseInterface
     */
    protected function respondWithCollection(ServerRequestInterface $request, ResponseInterface $response, $collection, TransformerAbstract $transformer, array $meta = [])
    {
        if (!isset($meta['total'])) $meta['total'] = count($collection);
        if (!isset($meta['collectionName'])) $meta['collectionName'] = static::$collectionName;
        $resource = new Collection($collection, $transformer);

        return $this->respond($request, $response, $resource, self::RESOURCE_TYPE_COLLECTION, 200, $meta);
    }

    /**
     * Respond with an Item.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param array|Persistent       $item        A resource item to transform and respond with.
     * @param TransformerAbstract    $transformer The transformer to apply to the item.
     *
     * @return ResponseInterface
     */
    protected function respondWithItem(ServerRequestInterface $request, ResponseInterface $response, $item, TransformerAbstract $transformer)
    {
        $resource = new Item($item, $transformer);

        return $this->respond($request, $response, $resource, self::RESOURCE_TYPE_ITEM);
    }

    /**
     * Respond to the successful creation of an Item.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param Persistent             $item
     * @param string                 $location
     * @param TransformerAbstract    $transformer
     *
     * @return ResponseInterface
     */
    protected function respondWithItemCreated(ServerRequestInterface $request, ResponseInterface $response, $item, $location, TransformerAbstract $transformer)
    {
        $resource = new Item($item, $transformer);

        return $this->respond(
            $request,
            $response,
            $resource,
            self::RESOURCE_TYPE_ITEM,
            201,
            ['headers' => ['Location' => $location]]
        );
    }

    /**
     * Respond to the request with a resource.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param ResourceInterface      $resource A fractal resource.
     * @param int                    $type     The type of resource to respond with.
     * @param int                    $status   The HTTP status code to respond with.
     * @param array                  $meta     An optional array of metadata for the response.
     *
     * @return ResponseInterface
     */
    protected function respond(ServerRequestInterface $request, ResponseInterface $response, $resource, $type = self::RESOURCE_TYPE_ITEM, $status = 200, $meta = array())
    {
        $bodyRaw = null;
        if ($resource !== null && $status !== 204) {
            $bodyRaw = [];
            $scope = $this->fractal->createData($resource)->toArray();
            switch ($type) {
                case self::RESOURCE_TYPE_ITEM:
                    $bodyRaw['_links'] = $this->refs();
                    foreach ($scope['data'] as $key => $value) {
                        $bodyRaw[$key] = $value;
                    }
                    break;
                case self::RESOURCE_TYPE_COLLECTION:
                    $links = array();
                    $total = $meta['total'];
                    $limit = isset($meta['limit']) && (int)$meta['limit'] > 0
                        ? (int)$meta['limit']
                        : isset($request->getQueryParams()['limit'])
                            ? (int)$request->getQueryParams()['limit']
                            : 25;
                    $offset = isset($meta['offset'])
                        ? (int)$meta['offset'] :
                        isset($request->getQueryParams()['offset'])
                            ? (int)$request->getQueryParams()['offset']
                            : 0;
                    if ($total > $offset) {
                        $links['first'] = static::ref($this->container, $request->getAttribute('route')->getArguments(), $offset > 0 ? ['offset' => 0] : [], '(First)');
                        $links['prev'] = ($offset > 0) ? static::ref($this->container, $request->getAttribute('route')->getArguments(), ['offset' => $offset - $limit], '(Previous)') : null;
                        $links['next'] = (($offset + $limit) < $total) ? static::ref($this->container, $request->getAttribute('route')->getArguments(), ['offset' => $offset + $limit], '(Next)') : null;
                        $links['last'] = static::ref($this->container, $request->getAttribute('route')->getArguments(), ['offset' => (floor(($total - 1) / $limit) * $limit)], '(Last)');
                    }
                    $bodyRaw['_links'] = $this->refs(array_filter($links));
                    $bodyRaw['_embedded'][$meta['collectionName']] = $scope['data'];
                    $bodyRaw['total_items'] = $total;
                    $bodyRaw['returned_items'] = count($scope['data']);
                    $bodyRaw['limit'] = $limit;
                    $bodyRaw['offset'] = $offset;
                    break;
                case self::RESOURCE_TYPE_ERROR:
                    $bodyRaw = $scope['data'];
                    break;
            }
        }

        /** @var Response $response */
        $response = $response->withBody(new Stream(fopen('php://temp', 'r+')));
        $response = $response->withHeader('Content-Type', static::$responseType);
        if (isset($meta['headers']) && is_array($meta['headers'])) {
            foreach ($meta['headers'] as $headerKey => $headerValue) {
                $response = $response->withHeader($headerKey, $headerValue);
            }
        }
        $response->withStatus($status);
        if ($bodyRaw !== null && $status !== 204) {
            if ($this->container->get('settings')['debug'] === true) {
                $bodyRaw['execution_time'] = microtime(true) - $this->container->get('settings')['startTime'];
            }
            $response->getBody()->write($this->encode($bodyRaw));
        }

        return $response;
    }

    /**
     * Check if the current method is allowed on this controller.
     *
     * @param ServerRequestInterface $request
     *
     * @throws MethodNotAllowedException
     */
    protected function checkMethod(ServerRequestInterface $request)
    {
        if (!in_array($request->getMethod(), static::$allowedMethods)) {
            throw new MethodNotAllowedException();
        }
    }

    /**
     * Generate a URL for the current requested route.
     *
     * @param array $params An array of parameters to add to the route.
     *
     * @return string The current route's URL.
     */
    protected function self(array $params = [])
    {
        /** @var ServerRequestInterface $request */
        $request = $this->container->get('request');
        $getParams = array_replace_recursive($request->getQueryParams(), $params);

        $uri = $request->getUri()->withQuery(http_build_query($getParams))->withUserInfo('');

        return (string)$uri;
    }
}
