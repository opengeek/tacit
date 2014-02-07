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
                    'class' => 'Tacit\\Model\\Monga\\MongaDatabase',
                    'server' => 'mongodb://localhost',
                    'options' => array('connect' => false),
                    'database' => 'test'
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
                'class' => 'Tacit\\Model\\Monga\\MongaDatabase',
                'server' => 'mongodb://localhost',
                'options' => array('connect' => false),
                'database' => 'test'
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

        $this->container->singleton('database', function () use ($configuration) {
            $dbClass = $configuration['connection']['class'];
            return new $dbClass($configuration['connection']);
        });

        $this->add(new MediaTypes());
    }
}
