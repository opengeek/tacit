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


use League\Monga\Database;
use Tacit\Model\Collection;

class MongaCollection extends Collection
{
    /**
     * A native connection for the Repository instance containing the collection.
     *
     * @var Database
     */
    protected $connection;

    /**
     * @var \League\Monga\Collection
     */
    protected $peer;

    /**
     * Create a new MongaCollection instance.
     *
     * @param string       $name       The name of the collection.
     * @param object|array $connection A reference to the native connection for the Repository containing the collection.
     * @param array        $options    An array of options for the collection.
     */
    public function __construct($name, &$connection, array $options = [])
    {
        parent::__construct($name, $connection, $options);
        $this->peer = $connection->collection($this->name);
    }

    /**
     * Cast MongoDB-specific data types to PHP-friendly data types
     *
     * @param mixed $var
     *
     * @return mixed
     */
    public function cast($var)
    {
        $casted = $var;
        if (is_object($var)) {
            $class = get_class($var);
            switch ($class) {
                case 'MongoId':
                    $casted = (string)$var;
                    break;
                case 'MongoDate':
                    $casted = (new \DateTime("@{$var->sec}"))->format(DATE_ISO8601);
                    break;
            }
        }
        return $casted;
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
        return $this->peer->count($query);
    }

    /**
     * Get an array of distinct values from an array field in the model.
     *
     * @param string $field The name of the field to get distinct values from.
     * @param null|array|\Closure $query The query to use to filter the collection.
     *
     * @return array An array of unique values in the specified field from a collection filtered by the query.
     */
    public function distinct($field, $query = null)
    {
        return $this->peer->distinct($field, $query);
    }

    /**
     * Drop the collection container from the Repository.
     *
     * @return bool Returns true if successfully dropped; false otherwise.
     */
    public function drop()
    {
        return $this->peer->drop();
    }

    /**
     * Find items in a collection based on the provided query.
     *
     * @param null|array|\Closure $query The query to use to filter the collection.
     * @param array               $fields An array of field names to include in the result.
     *
     * @return array An array of items found; otherwise an empty array.
     */
    public function find($query, $fields = [])
    {
        return $this->peer->find($query, $fields);
    }

    /**
     * Find an item in a collection based on the provided query.
     *
     * @param null|array|\Closure $query The query to use to select the item.
     * @param array               $fields An array of field names to include in the result.
     *
     * @return null|array The Persistent item array result if found; otherwise null.
     */
    public function findOne($query, $fields = [])
    {
        return $this->peer->findOne($query, $fields);
    }

    /**
     * Insert an item into a collection with the provided data.
     *
     * @param array $data An array of data representing the properties of the item.
     * @param array $options Options for the insert.
     *
     * @return mixed The unique key of the inserted item or false.
     */
    public function insert($data, $options = [])
    {
        return $this->peer->insert($data, $options);
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
        return $this->peer->remove($query);
    }

    /**
     * Remove all items from a repository collection.
     *
     * @return bool Returns true on successful truncation; false on failure.
     */
    public function truncate()
    {
        return $this->peer->truncate();
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
        return $this->peer->update($data, $query, $options);
    }
}
