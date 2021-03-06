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


use DateTime;
use DateTimeZone;
use r\Cursor;
use r\Queries\Selecting\Get;
use r\Queries\Tables\Table;
use r\ValuedQuery\ValuedQuery;

class Collection extends \Tacit\Model\Collection
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var Table
     */
    protected $peer;

    /**
     * Construct a new RethinkDB Collection.
     *
     * @param string       $name       The name of the collection.
     * @param object|array $connection A reference to the native connection for the Repository containing the collection.
     * @param array        $options    An array of options for the collection.
     */
    public function __construct($name, &$connection, array $options = [])
    {
        parent::__construct($name, $connection, $options);
        $this->peer = \r\table($name, $this->options);
    }

    /**
     * Cast repository-specific data types to PHP-friendly data types.
     *
     * @param mixed $var
     *
     * @return mixed
     */
    public function cast($var)
    {
        if (is_object($var)) {
            $class = get_class($var);
            switch ($class) {
                case 'DateTime':
                    /** @var DateTime $var */
                    $var = $var->setTimezone(new DateTimeZone('UTC'))->format(DATE_ISO8601);
                    break;
            }
        }
        return $var;
    }

    /**
     * Count the number of items in a collection with an optional query.
     *
     * @param null|array|\Closure $query The query to use to filter the collection.
     *
     * @return int The number of items in the collection filtered by the query.
     */
    public function count($query = [])
    {
        return $this->sequence($query)->count()->run($this->connection->getHandle());
    }

    /**
     * Get an array of distinct values from an array field in the model.
     *
     * @param string              $field The name of the field to get distinct values from.
     * @param null|array|\Closure $query The query to use to filter the collection.
     *
     * @return array An array of unique values in the specified field from a collection filtered by the query.
     */
    public function distinct($field, $query = null)
    {
        $result = $this->sequence(null)->distinct(['index' => $field]);
        if (!empty($query)) {
            $result = $result->filter($query);
        }
        $result = $result->run($this->connection->getHandle());
        if ($result instanceof Cursor) {
            $result = $result->toArray();
        }

        return $result;
    }

    /**
     * Drop the collection container from the Repository.
     *
     * @return bool Returns true if successfully dropped; false otherwise.
     */
    public function drop()
    {
        return $this->connection->getDb()->tableDrop($this->name)->run($this->connection->getHandle());
    }

    /**
     * Find items in a collection based on the provided query.
     *
     * @param null|array|\Closure $query The query to use to filter the collection.
     * @param array               $fields An array of field names to include in the result.
     *
     * @return array|ValuedQuery An array of items found; otherwise an empty array.
     */
    public function find($query, $fields = [])
    {
        $result = $this->sequence($query);
        if (!empty($fields)) {
            $result = $result->pluck($fields);
        }
        $result = $result->run($this->connection->getHandle());
        if ($result instanceof Cursor) {
            $result = $result->toArray();
        }

        return $result;
    }

    /**
     * Find an item in a collection based on the provided query.
     *
     * @param null|array|\Closure $query The query to use to select the item.
     * @param array               $fields An array of field names to include in the result.
     *
     * @return null|array|ValuedQuery The Persistent item array result if found; otherwise null.
     */
    public function findOne($query, $fields = [])
    {
        $result = $this->sequence($query);
        if ($result instanceof Get) {
            if (!empty($fields)) {
                $result = $result->pluck($fields);
            }
            $result = $result->run($this->connection->getHandle());
        } else {
            if (!empty($fields)) {
                $result = $result->pluck($fields);
            }
            $result = $result->limit(1)->run($this->connection->getHandle());
        }
        if ($result instanceof Cursor) {
            $result = $result->toArray();
            $result = array_pop($result);
        }
        return $result;
    }

    /**
     * Insert an item into a collection with the provided data.
     *
     * @param array $data An array of data representing the properties of the item.
     * @param array $options Options for the insert.
     *
     * @return mixed The generated key of the inserted item if not specified, true, or false.
     */
    public function insert($data, $options = [])
    {
        $result = $this->peer->insert($data, $options)->run($this->connection->getHandle());
        if ($result->offsetGet('inserted') == 1) {
            if ($result->offsetExists('generated_keys') && is_array($result->offsetGet('generated_keys'))) {
                return $result->offsetGet('generated_keys')[0];
            }
            return true;
        }
        return false;
    }

    /**
     * Remove one or more items from a repository collection.
     *
     * @param null|array|\Closure $query The query to use to select the items for removal.
     *
     * @return int|bool The number of items removed or false on failure.
     */
    public function remove($query)
    {
        $result = $this->sequence($query)->delete()->run($this->connection->getHandle());
        return $result->offsetExists('deleted') && $result->offsetGet('errors') == 0 ? $result->offsetGet('deleted') : false;
    }

    /**
     * Remove all items from a repository collection.
     *
     * @return bool Returns true on successful truncation; false on failure.
     */
    public function truncate()
    {
        $result = $this->peer->delete()->run($this->connection->getHandle());
        return $result->offsetExists('deleted') && $result->offsetGet('errors') == 0 ? true : false;
    }

    /**
     * Update one or more items in a collection with the provided data.
     *
     * @param null|array|\Closure $query The query to use to select the items.
     * @param array               $data An array of data presenting the update.
     * @param array               $options An array of options for the process.
     *
     * @return int|bool The number of items updated or false on failure.
     */
    public function update($query, $data, $options = [])
    {
        $result = $this->sequence($query)->update($data, $options)->run($this->connection->getHandle());
        return $result->offsetExists('replaced') && $result->offsetGet('errors') == 0 ? $result->offsetGet('replaced') : false;
    }

    /**
     * @param $query
     * @return ValuedQuery
     */
    private function sequence($query)
    {
        $result = $this->peer;
        if ($query !== null) {
            if ($query instanceof \Closure) {
                $query = $query($this->peer);
            }
            if (is_array($query) && isset($query['id']) && count($query) === 1) {
                $query = $this->peer->get($query['id']);
            } elseif (is_array($query) && !empty($query)) {
                $query = $this->peer->filter($query);
            }
            if (!empty($query)) {
                $result = $query;
            }
        }

        return $result;
    }
}
