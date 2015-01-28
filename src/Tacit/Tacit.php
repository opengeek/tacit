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
 * An extension of Slim to wrap RESTful RAD server features.
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
        $configuration = $this->loadConfiguration($configuration);

        parent::__construct($configuration);

        $connection = $this->config('connection');

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

        if ($connection !== null) {
            $this->container->singleton('repository', function () use ($connection) {
                $dbClass = $connection['class'];
                return new $dbClass($connection);
            });
        }

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

    /**
     * Load the configuration from a file stream or array.
     *
     * @param string|array $configuration A configuration array or stream path.
     *
     * @return array The configuration array.
     */
    protected function loadConfiguration($configuration)
    {
        if (is_string($configuration) && is_readable($configuration)) {
            $file = new \SplFileInfo($configuration);
            switch ($file->getExtension()) {
                case 'json':
                    $configuration = json_decode(file_get_contents($configuration), true);
                    break;
                case 'php':
                    $configuration = include $configuration;
                    break;
            }
        }
        if (!is_array($configuration)) {
            $configuration = [
                'mode' => 'development',
                'startTime' => microtime(true),
                'connection' => [
                    'class' => 'Tacit\\Model\\Monga\\MongaRepository',
                    'server' => 'mongodb://localhost',
                    'options' => array('connect' => false),
                    'repository' => 'test'
                ]
            ];
        }
        if (!array_key_exists('connection', $configuration)) {
            $configuration['connection'] = [
                'class' => 'Tacit\\Model\\Monga\\MongaRepository',
                'server' => 'mongodb://localhost',
                'options' => array('connect' => false),
                'repository' => 'test'
            ];
        }
        if (!isset($configuration['startTime'])) {
            $configuration['startTime'] = microtime(true);
        }
        return $configuration;
    }
}
