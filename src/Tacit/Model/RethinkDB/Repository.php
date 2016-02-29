<?php
/*
 * This file is part of the Tacit package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tacit\Model\RethinkDB;


class Repository extends \Tacit\Model\Repository
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * Construct a new RethinkDB Repository instance.
     *
     * @param array $configuration An array of configuration data for the instance.
     */
    public function __construct(array $configuration = array())
    {
        $server = isset($configuration['server']) ? $configuration['server'] : '127.0.0.1';
        $port   = isset($configuration['port']) ? $configuration['port'] : null;

        $this->connection = new Connection(
            \r\connect($server, $port, $configuration['repository']),
            \r\db($configuration['repository'])
        );
    }

    /**
     * Get a specific collection container from the Repository.
     *
     * @param string $name The name of the collection to get.
     *
     * @return Collection A native collection instance wrapped by a Tacit\Model\Collection.
     */
    public function collection($name)
    {
        return new Collection($name, $this->connection);
    }
}
