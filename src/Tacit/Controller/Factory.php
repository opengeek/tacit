<?php
/*
 * This file is part of the Denizen package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tacit\Controller;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tacit\Container;

/**
 * A factory for building route handlers for Tacit controllers.
 *
 * @package Tacit\Controller
 */
class Factory
{
    /** @var Container */
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Return a Closure representing the route handler for the specified Restful controller.
     *
     * @param $controller
     *
     * @return \Closure
     */
    public function restful($controller)
    {
        $container = $this->container;

        return function(ServerRequestInterface $request, ResponseInterface $response, array $args = []) use ($container, $controller) {
            /** @var Restful $handler */
            $handler = new $controller($container->settings, $container->router, $container->fractal);

            return $handler->handle($request, $response, $args);
        };
    }

    /**
     * Return a Closure representing the route handler for the specified RestfulItem controller.
     *
     * @param $controller
     *
     * @return \Closure
     */
    public function item($controller)
    {
        $container = $this->container;

        return function(ServerRequestInterface $request, ResponseInterface $response, array $args = []) use ($container, $controller) {
            /** @var Restful $handler */
            $handler = new $controller($container->settings, $container->router, $container->fractal, $container->repository);

            return $handler->handle($request, $response, $args);
        };
    }

    /**
     * Return a Closure representing the route handler for the specified RestfulCollection controller.
     *
     * @param $controller
     *
     * @return \Closure
     */
    public function collection($controller)
    {
        $container = $this->container;

        return function(ServerRequestInterface $request, ResponseInterface $response, array $args = []) use ($container, $controller) {
            /** @var Restful $handler */
            $handler = new $controller($container->settings, $container->router, $container->fractal, $container->repository);

            return $handler->handle($request, $response, $args);
        };
    }
}
