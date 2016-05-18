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

use League\Fractal\Manager;
use Slim\Collection;
use Tacit\Handlers\Error;
use Tacit\Model\Repository;


/**
 * @property-read Error $errorHandler
 * @property-read Collection $settings
 * @property-read Repository|null $repository
 *
 * @package Tacit
 */
class Container extends \Slim\Container
{
    public function __construct(array $values)
    {
        parent::__construct($values);

        $this->registerCustomServices();
    }
    
    protected function registerCustomServices()
    {
        $this['errorHandler'] = function(Container $c) {
            return new Error($c->settings->all());
        };

        $this['repository'] = function(Container $c) {
            $connection = $c->settings->get('connection');
            if ($connection === null) return null;
            
            $dbClass = $connection['class'];

            return new $dbClass($connection);
        };

        $this['fractal'] = function() {
            return new Manager();
        };
    }
}
