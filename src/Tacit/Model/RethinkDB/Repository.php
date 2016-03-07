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


use Exception;
use Tacit\Model\Exception\RepositoryException;

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
        parent::__construct($configuration);

        $server = $this->option('server', '127.0.0.1');
        $port   = $this->option('port');

        $this->connection = new Connection(
            \r\connect($server, $port, $this->option('repository')),
            \r\db($this->option('repository'))
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

    public function create(array $options = [])
    {
        try {
            \r\dbCreate($this->option('repository'))->run($this->connection->getHandle());
        } catch (Exception $e) {
            if ($this->option('exceptions', true, $options)) {
                throw new RepositoryException("Could not create RethinkDB database {$this->option('repository')}", $e->getCode(), $e);
            }
        }
    }

    public function destroy(array $options = [])
    {
        try {
            \r\dbDrop($this->option('repository'))->run($this->connection->getHandle());
        } catch (Exception $e) {
            if ($this->option('exceptions', true, $options)) {
                throw new RepositoryException("Could not drop RethinkDB database {$this->option('repository')}", $e->getCode(), $e);
            }
        }
    }
}
