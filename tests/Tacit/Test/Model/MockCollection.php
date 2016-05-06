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

class MockCollection extends Collection
{
    /**
     * @var array
     */
    protected $connection;

    public function __construct($name, &$connection, array $options = [])
    {
        parent::__construct($name, $connection, $options);
    }

    /**
     * Cast data types to PHP-friendly data types
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
                case 'DateTime':
                    /** @var \DateTime $var */
                    $casted = $var->format(DATE_ISO8601);
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
        if (empty($query)) return count($this->connection[$this->name]);
        $filtered = $this->filter($this->connection[$this->name], $query);
        return count($filtered);
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
        if (null !== $query) {
            $collection = $this->filter($this->connection[$this->name], $query);
        } else {
            $collection = $this->connection[$this->name];
        }

        $distinct = [];
        foreach ($collection as $id => $item) {
            if (isset($item[$field])) {
                if (is_array($item[$field])) {
                    foreach ($item[$field] as $value) {
                        $distinct[] = $value;
                    }
                } else {
                    $distinct[] = $item[$field];
                }
            }
        }
        $distinct = array_unique($distinct);
        sort($distinct);
        return $distinct;
    }

    /**
     * Drop the collection container from the Repository.
     *
     * @return bool Returns true if successfully dropped; false otherwise.
     */
    public function drop()
    {
        return $this->truncate();
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
        $filtered = $this->filter($this->connection[$this->name], $query);
        return $this->mask($filtered, $fields);
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
        $found = $this->find($query, $fields);
        return reset($found);
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
        $id = sha1(uniqid('', true));
        $data['_id'] = $id;
        if (!isset($this->connection[$this->name])) {
            $this->connection[$this->name] = [];
        }
        $this->connection[$this->name][$id] = $data;
        return $id;
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
        $removeable = $this->filter($this->connection[$this->name], $query);
        foreach ($removeable as $idx => $removeMe) {
            unset($this->connection[$this->name][$idx]);
        }
        return count($removeable);
    }

    /**
     * Remove all items from a repository collection.
     *
     * @return bool Returns true on successful truncation; false on failure.
     */
    public function truncate()
    {
        $this->connection[$this->name] = [];
        return true;
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
        $filtered = $this->filter($this->connection[$this->name], $query);
        foreach ($filtered as $idx => $data) {
            $this->connection[$this->name][$idx] = array_merge($this->connection[$this->name][$idx], $data);
        }
        return count($filtered);
    }

    /**
     * Filter a collection.
     *
     * @param array          $collection The collection to filter.
     * @param array|\Closure $query An associative array to filter by or a Closure
     * to run via array_map.
     *
     * @return array The filtered collection.
     */
    private function filter($collection, $query = [])
    {
        if (!$query instanceof \Closure) {
            if (empty($query) || !is_array($query)) {
                return $collection;
            }
            $query = function ($value) use ($query) {
                $intersection = array_intersect_assoc($query, $value);
                return ($intersection === $query);
            };
        }
        return array_filter($collection, $query);
    }

    /**
     * Mask a collection.
     *
     * @param array $collection An collection of arrays to mask.
     * @param array $mask The array of field names to include in the masked collection.
     *
     * @return array The masked collection.
     */
    private function mask($collection, $mask = [])
    {
        if (is_array($mask) && !empty($mask)) {
            array_walk($collection, function (&$value) use ($mask) {
                $value = array_intersect_key($value, array_flip($mask));
            });
        }
        return $collection;
    }
}
