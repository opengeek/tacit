<?php
/*
 * This file is part of the Tacit package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tacit;


use Slim\Slim;
use Tacit\Controller\Exception\NotFoundException;
use Tacit\Controller\Exception\RestfulException;

/**
 * An extension of Slim to hide the RESTful RAD server features.
 *
 * @package Tacit
 */
class Tacit extends Slim
{
    /**
     * Construct a new instance of Tacit.
     *
     * @param string|array|null $configuration An array or file that returns an
     * array when included.
     */
    public function __construct($configuration = null)
    {
        if (is_string($configuration) && is_readable($configuration)) {
            $configuration = include $configuration;
        }
        if (!is_array($configuration)) {
            $configuration = [
                'app' => [
                    'mode' => 'development',
                    'startTime' => microtime(true)
                ],
                'connection' => [
                    'class' => 'Tacit\\Model\\Monga\\MongaRepository',
                    'server' => 'mongodb://localhost',
                    'options' => array('connect' => false),
                    'repository' => 'test'
                ]
            ];
        }
        if (!isset($configuration['app']) || !is_array($configuration['app'])) {
            $configuration['app'] = [
                'mode' => 'development',
                'startTime' => microtime(true)
            ];
        }

        parent::__construct($configuration['app']);

        if (!isset($configuration['connection']) || !is_array($configuration['connection'])) {
            $configuration['connection'] = [
                'class' => 'Tacit\\Model\\Monga\\MongaRepository',
                'server' => 'mongodb://localhost',
                'options' => array('connect' => false),
                'repository' => 'test'
            ];
        }

        $this->configureMode('production', function () {
            $this->config([
                'log.enable' => true,
                'debug' => false
            ]);
        });
        $this->configureMode('development', function () {
            $this->config([
                'log.enable' => false,
                'debug' => true
            ]);
        });

        $this->container->singleton('repository', function () use ($configuration) {
            $dbClass = $configuration['connection']['class'];
            return new $dbClass($configuration['connection']);
        });

        $this->add(new MediaTypes());

        $this->error(function (\Exception $e) {
            $resource = [
                'status' => 500,
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ];
            if ($e instanceof RestfulException) {
                if ($e instanceof NotFoundException) {
                    $this->notFound();
                }
                $resource['status'] = $e->getStatus();
                $resource['description'] = $e->getDescription();
                $resource['property'] = $e->getProperty();
            }

            /* @var \Slim\Http\Response $response */
            $response = $this->response;
            $response->headers->set('Content-Type', 'application/json');
            $response->setStatus($resource['status']);
            if ($this->config('debug') === true) {
                $resource['request_duration'] = microtime(true) - $this->config('startTime');
            }
            $response->setBody(json_encode($resource));

            $this->stop();
        });

        $this->notFound(function () {
            $resource = [
                'status' => 404,
                'message' => 'Resource not found'
            ];

            /* @var \Slim\Http\Response $response */
            $response = $this->response;
            $response->headers->set('Content-Type', 'application/json');
            $response->setStatus(404);
            if ($this->config('debug') === true) {
                $resource['request_duration'] = microtime(true) - $this->config('startTime');
            }
            $response->setBody(json_encode($resource));

            $this->stop();
        });
    }
}
