<?php
/*
 * This file is part of the Tacit package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tacit\Test\Model;

use Tacit\Model\Collection;
use Tacit\Model\Exception\RepositoryException;
use Tacit\Model\Repository;

/**
 * A mock Repository implementation.
 *
 * @package Tacit\Test\Model
 */
class MockRepository extends Repository
{
    /**
     * The native MockRepository connection.
     *
     * @var array
     */
    protected $connection;

    /**
     * Construct a new MockRepository instance.
     *
     * @param array $configuration An array of configuration data for the instance.
     */
    public function __construct(array $configuration = array())
    {
        $this->connection = [];
    }

    /**
     * Get a specific collection/table from the Repository.
     *
     * @param string $name The name of the collection to get.
     *
     * @return Collection A native collection instance wrapped by a Tacit\Model\Collection.
     */
    public function collection($name)
    {
        return new MockCollection($name, $this->connection);
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
    public function create(array $options = [])
    {

    }

    /**
     * Destroy the Repository container (aka database).
     *
     * @param array $options An array of options for container destruction.
     *
     * @throws RepositoryException If container destruction fails and exceptions
     * are not disabled.
     */
    public function destroy(array $options = [])
    {
        
    }
}
