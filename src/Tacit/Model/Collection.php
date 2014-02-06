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
 * Defines the behaviors and properties of a model Collection.
 *
 * @package Tacit\Model
 */
trait Collection
{
    /**
     * @var string The name of the collection.
     */
    public $name;

    /**
     * @var Database The database instance containing the collection.
     */
    protected $database;

    /**
     * Get the public fields of an object as an array.
     *
     * @param object $object An object to get public fields from.
     *
     * @return array An associative array of public fields from the object.
     */
    public static function getPublicVars($object)
    {
        return get_object_vars($object);
    }

    /**
     * Get a field mask consisting of public fields minus excludes.
     *
     * @param object $object An object to produce a mask for.
     * @param array  $exclude An array of fields to exclude from the mask.
     * @param array  $excludeDefaults An array of default fields to exclude from
     * the mask.
     *
     * @return array A mask of public field names for an object minus various excludes.
     */
    public static function getMask($object, array $exclude = [], array $excludeDefaults = ['_id', '_type', 'password'])
    {
        $mask = array_keys(self::getPublicVars($object));
        return array_diff($mask, $exclude, $excludeDefaults);
    }

    /**
     * Create a new instance of the Collection.
     *
     * @param string $name The name of the collection.
     * @param Database $database The database containing the collection.
     */
    public function __construct($name, $database)
    {
        $this->name = $name;
        $this->database = $database;
    }

    /**
     * Cast database-specific data types to PHP-friendly data types.
     *
     * @param mixed $var
     *
     * @return mixed
     */
    abstract public function cast($var);

    /**
     * Count the number of items in a collection with an optional query.
     *
     * @param null|array|\Closure $query The query to use to filter the collection.
     *
     * @return int The number of items in the collection filtered by the query.
     */
    abstract public function count($query);

    /**
     * Find items in a collection based on the provided query.
     *
     * @param null|array|\Closure $query The query to use to filter the collection.
     * @param array $fields An array of field names to include in the result.
     *
     * @return array An array of items found; otherwise an empty array.
     */
    abstract public function find($query, $fields = []);

    /**
     * Find an item in a collection based on the provided query.
     *
     * @param null|array|\Closure $query The query to use to select the item.
     * @param array $fields An array of field names to include in the result.
     *
     * @return null|array The Persistent item array result if found; otherwise null.
     */
    abstract public function findOne($query, $fields = []);

    /**
     * Insert an item into a collection with the provided data.
     *
     * @param array $data An array of data representing the properties of the item.
     * @param array $options Options for the insert.
     *
     * @return mixed The unique key of the inserted item or false.
     */
    abstract public function insert($data, $options = []);

    /**
     * Remove one or more items from a database collection.
     *
     * @param null|array|\Closure $query The query to use to select the items for removal.
     *
     * @return int|bool The number of items removed or false on failure.
     */
    abstract public function remove($query);

    /**
     * Update one or more items in a collection with the provided data.
     *
     * @param null|array|\Closure $query The query to use to select the items.
     * @param array $data An array of data presenting the update.
     * @param array $options An array of options for the process.
     *
     * @return int|bool The number of items updated or false on failure.
     */
    abstract public function update($query, $data, $options = []);
}
