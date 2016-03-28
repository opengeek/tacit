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


use Slim\App;
use Slim\Collection;
use Tacit\Model\Repository;

/**
 * A wrapper for Slim to provide RESTful RAD server features.
 *
 * @property Repository|null $repository
 *
 * @package Tacit
 */
class Tacit extends App
{
    /**
     * Construct a new instance of Tacit.
     *
     * @param string|array|Container|null $configuration An array or file that returns an
     * array when included.
     */
    public function __construct($configuration = null)
    {
        $configuration = $this->loadConfiguration($configuration);

        $container = isset($configuration['container']) ? $configuration['container'] : null;
        unset($configuration['container']);

        if (!$container instanceof Container) {
            $container = new Container(['settings' => $configuration]);
        }

        parent::__construct($container);

        $connection = isset($configuration['connection'])
            ? $configuration['connection']
            : null;

        if ($connection !== null) {
            $this->getContainer()['repository'] = function () use ($connection) {
                $dbClass = $connection['class'];
                return new $dbClass($connection);
            };
        }
    }

    /**
     * Get a configuration value from the app settings.
     *
     * @param string $key
     * @param mixed  $value
     * @return mixed
     */
    public function config($key, $value = null)
    {
        /** @var Collection $settings */
        $settings = $this->getContainer()['settings'];

        if ($value !== null) {
            $settings->set($key, $value);

            return true;
        }
        if ($settings->has($key)) {

            return $settings->get($key);
        }

        return null;
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
            ];
        }
        if (!isset($configuration['startTime'])) {
            $configuration['startTime'] = microtime(true);
        }
        return $configuration;
    }

    /**
     * Similar as run method, but returns Response instead of echoing it
     *
     * @param array $mock
     *
     * @return \Slim\Http\Response
     * @throws \Slim\Exception\MethodNotAllowedException
     * @throws \Slim\Exception\NotFoundException
     */
    public function invoke(array $mock)
    {
        return $this->__invoke($mock['request'], $mock['response']);
    }
}
