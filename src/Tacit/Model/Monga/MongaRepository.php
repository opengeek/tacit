<?php
/*
 * This file is part of the Tacit package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tacit\Model\Monga;


use Exception;
use League\Monga;
use League\Monga\Database;
use Tacit\Model\Collection;
use Tacit\Model\Exception\RepositoryException;
use Tacit\Model\Repository;

/**
 * Repository bridge for the Monga library for MongoDB.
 *
 * @package Tacit\Model\Monga
 */
class MongaRepository extends Repository
{
    /**
     * The native Monga Database instance.
     *
     * @var Database
     */
    protected $connection;

    /**
     * Construct a new MongaRepository instance.
     *
     * @param array $configuration An array of configuration data for the instance.
     */
    public function __construct(array $configuration = array())
    {
        parent::__construct($configuration);

        /* for Monga, we want the "Database" object as the "native connection" */
        $this->connection = Monga::connection(
            $configuration['server'],
            $configuration['options']
        )->database($configuration['repository']);
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
        return new MongaCollection($name, $this->connection);
    }

    /**
     * Get the "native" Monga Database class.
     *
     * @return Database
     */
    public function getConnection()
    {
        return parent::getConnection();
    }

    public function create(array $options = [])
    {
        if ($this->option('exceptions', true, $options)) {
            $collections = $this->connection->listCollections();
            if (!empty($collections)) {
                throw new RepositoryException("The MongoDB database {$this->option('repository')} already exists and has existing collections");
            }
        }
    }

    public function destroy(array $options = [])
    {
        $dropped = false;
        try {
            $dropped = $this->connection->drop();
        } catch (Exception $e) {
            if ($this->option('exceptions', true, $options)) {
                throw new RepositoryException("Could not drop MongoDB database {$this->option('repository')}", $e->getCode(), $e);
            }
        }
        if ($dropped === false && $this->option('exceptions', true, $options)) {
            throw new RepositoryException("Could not drop MongoDB database {$this->option('repository')}");
        }
    }
}
