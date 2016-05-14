<?php
/*
 * This file is part of the Tacit package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tacit\Model;

use Slim\Collection;
use Tacit\Model\Exception\RepositoryException;

/**
 * Defines the contract for a model Repository.
 *
 * @package Tacit\Model
 */
abstract class Repository
{
    /**
     * A native connection object for a repository.
     *
     * @var object
     */
    protected $connection;

    /**
     * The configuration for a repository.
     *
     * @var Collection
     */
    private $configuration;

    /**
     * Repository constructor.
     *
     * @param array $configuration
     */
    public function __construct(array $configuration = [])
    {
        $this->configuration = new Collection($configuration);
    }

    /**
     * Return the native connection object for the repository.
     *
     * @return object
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Create the Repository container (aka database).
     *
     * This method MUST throw exceptions by default and MUST provide an option for
     * disabling exceptions, i.e. ['exceptions' => false].
     *
     * @param array $options An array of options for creating the container.
     *
     * @throws RepositoryException If container creation fails and exceptions are
     * not disabled.
     */
    abstract public function create(array $options = []);

    /**
     * Get a specific collection container from the Repository.
     *
     * @param string $name The name of the collection to get.
     * @param array  $options An array of options for the Collection.
     *
     * @return Collection A native collection instance wrapped by a Tacit\Model\Collection.
     */
    abstract public function collection($name, array $options = []);

    /**
     * Destroy the Repository container (aka database).
     *
     * @param array $options An array of options for container destruction.
     *
     * @throws RepositoryException If container destruction fails and exceptions
     * are not disabled.
     */
    abstract public function destroy(array $options = []);

    /**
     * Get a configuration option for the Repository.
     *
     * @param string $key The key of the configuration option.
     * @param mixed $default A default value to use if not set in the configuration.
     * @param array $options An array of options to override those of the Repository.
     *
     * @return mixed The value of the configuration option specified by key.
     */
    public function option($key, $default = null, array $options = [])
    {
        if (!empty($options) && array_key_exists($key, $options)) {
            return $options[$key];
        }

        return $this->configuration->get($key, $default);
    }
}
