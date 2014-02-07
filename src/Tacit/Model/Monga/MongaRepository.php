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


use League\Monga;
use Tacit\Model\Collection;
use Tacit\Model\Repository;

/**
 * Repository bridge for the Monga library for MongoDB.
 *
 * @package Tacit\Model\Monga
 */
class MongaRepository extends Repository
{
    /**
     * The native Monga\Database instance.
     *
     * @var Monga\Database
     */
    protected $connection;

    /**
     * Construct a new MongaRepository instance.
     *
     * @param array $configuration An array of configuration data for the instance.
     */
    public function __construct(array $configuration = array())
    {
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
}
