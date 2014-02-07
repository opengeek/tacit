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

/**
 * Defines the contract for a model Repository.
 *
 * @package Tacit\Model
 */
abstract class Repository
{
    /**
     * @var static
     */
    private static $instance;

    /**
     * A native connection object for a repository.
     *
     * @var object
     */
    protected $connection;

    /**
     * Get a Repository instance (singleton).
     *
     * @param array $configuration An array containing the configuration necessary to
     * get a connection to and identify the repository within a repository.
     *
     * @return static
     */
    public static function getInstance(array $configuration = [])
    {
        if (null === self::$instance) {
            self::$instance = new static($configuration);
        }
        return self::$instance;
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
     * Get a specific collection container from the Repository.
     *
     * @param string $name The name of the collection to get.
     *
     * @return Collection A native collection instance wrapped by a Tacit\Model\Collection.
     */
    abstract public function collection($name);
}
