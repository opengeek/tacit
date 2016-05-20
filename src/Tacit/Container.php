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
use Tacit\Controller\Factory;
use Tacit\Handlers\Error;
use Tacit\Model\Repository;


/**
 * @property-read Error $errorHandler
 * @property-read Collection $settings
 * @property-read Manager $fractal
 * @property-read Factory $controllers
 * @property-read Repository|null $repository
 *
 * @package Tacit
 */
class Container extends \Slim\Container
{
    /**
     * Create a new Container for your application.
     *
     * @param array $values The parameters or objects.
     */
    public function __construct(array $values)
    {
        parent::__construct($values);

        $this->registerCustomServices();

        $this->registerRepository();
    }

    /**
     * Register custom services for this application.
     */
    protected function registerCustomServices()
    {
        $this['errorHandler'] = function(Container $c) {
            return new Error($c->settings->all());
        };

        $this['fractal'] = function() {
            return new Manager();
        };

        $this['controllers'] = function(Container $c) {
            return new Factory($c);
        };
    }

    /**
     * Register the Repository for this container.
     */
    protected function registerRepository()
    {
        $this['repository'] = function(Container $c) {
            $connection = $c->settings->get('connection');
            if ($connection === null) return null;

            return $this->repositoryFactory($connection);
        };
    }

    /**
     * A Repository factory for your application.
     *
     * Override this to construct a Repository instance with all required dependencies.
     *
     * @param array     $connection An array of settings containing the Repository connection details.
     *
     * @return Repository
     */
    protected function repositoryFactory(array $connection)
    {
        return new $connection['class']($connection);
    }
}
