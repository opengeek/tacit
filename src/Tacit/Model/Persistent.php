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


use Tacit\Tacit;
use Tacit\Validate\Validator;

/**
 * Defines the behaviors and properties of a Persistent model object.
 *
 * @package Tacit\Model
 */
trait Persistent
{
    use Routable;

    /**
     * @var Collection The Collection represents a collection/table in a Database
     */
    protected static $collection;

    /**
     * @var string The name of the collection/table within it's Database
     */
    protected static $collectionName;

    /**
     * @var array An array of Rules to use when validating objects if this class.
     */
    protected static $validationRules;

    /**
     * An array containing keys of fields that are dirty for this model item.
     *
     * @var array
     */
    protected $_dirty = array();

    /**
     * The name of a field representing the unique identifier for this model item.
     *
     * @var string
     */
    protected $_keyField;

    /**
     * A Validator instance for this model item.
     *
     * @var Validator
     */
    protected $_validator;

    /**
     * Get the Collection from the Database.
     *
     * @return Collection
     */
    public static function collection()
    {
        /** @var Database $database */
        $database = Tacit::getInstance()->container->get('database');
        static::$collection = $database->collection(static::$collectionName);
        return static::$collection;
    }

    /**
     * Get the name of the Collection in the Database.
     *
     * @return string
     */
    public static function collectionName()
    {
        return self::$collectionName;
    }

    /**
     * Create an instance of this model class in the persistence provider.
     *
     * @param array $data
     *
     * @throws ModelCreateException
     * @throws ModelValidationException
     * @return Persistent|null
     */
    public static function create(array $data = array())
    {
        try {
            $instance = static::instance($data);
            $result = $instance->save();
        } catch (ModelValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new ModelCreateException("Error creating item in collection " . static::$collectionName, $e->getCode(), $e);
        }
        if (false === $result) {
            throw new ModelCreateException("Error creating item in collection " . static::$collectionName);
        }
        return $instance;
    }

    /**
     * Create an instance of this model class.
     *
     * @param array $data
     * @return Persistent|null
     */
    public static function instance(array $data = array())
    {
        /** @var Persistent $instance */
        $instance = new static();
        $instance->hydrate($data);
        return $instance;
    }

    /**
     * Find Documents within this Collection meeting the specified criteria.
     *
     * @param array|\Closure $criteria
     * @param array $fields An array of fields to be returned from the Document.
     * All fields are returned if not provided.
     * @return array|null
     */
    public static function find($criteria = array(), array $fields = array())
    {
        $collection = null;
        $models = static::collection()->find($criteria, $fields);
        if ($models) {
            $collection = array();
            foreach ($models as $id => $model) {
                /** @var Persistent $object */
                $object = new static();
                $object->hydrate($model);
                $collection[$id] = $object;
            }
        }
        return $collection;
    }

    /**
     * Count Documents within this Collection meeting the specified criteria.
     *
     * @param array|\Closure $criteria
     * @return int
     */
    public static function count($criteria = array())
    {
        return static::collection()->count($criteria);
    }

    /**
     * Find a Document within this Collection meeting the specified criteria.
     *
     * @param array|\Closure $criteria
     * @param array $fields
     * @return Persistent|null
     */
    public static function findOne($criteria, array $fields = array())
    {
        $instance = null;
        if ($model = static::collection()->findOne($criteria, $fields)) {
            /** @var Persistent $instance */
            $instance = new static();
            $instance->hydrate($model);
        }
        return $instance;
    }

    /**
     * Update one or more Documents in this Collection meeting the specified criteria.
     *
     * @param array|\Closure $criteria
     * @param array $fields
     * @param array $options
     *
     * @return bool
     */
    public static function update($criteria, array $fields, array $options = ['w' => 1])
    {
        return static::collection()->update($fields, $criteria, $options);
    }

    /**
     * Get a set of validation rules for this model.
     *
     * @param array $rules Optional rules to add explicitly.
     *
     * @return array An array of validation rules grouped by field.
     */
    public static function validationRules(array $rules = [])
    {
        return array_merge_recursive(static::$validationRules, $rules);
    }

    /**
     * Get an array of properties with dirty values.
     *
     * @return array
     */
    public function dirty()
    {
        $dirty = array();
        foreach ($this->_dirty as $key) {
            $dirty[$key] = $this->get($key);
        }
        return $dirty;
    }

    /**
     * Get a property of this model.
     *
     * @param string $key
     * @param null|mixed $default
     * @param null|\Closure $formatter
     * @return mixed|null
     */
    public function get($key, $default = null, $formatter = null)
    {
        $value = $default;
        if (isset($this->$key)) {
            $value = $this->$key;
        }
        if (null !== $formatter && $formatter instanceof \Closure) {
            $value = $formatter($value, $key);
        }
        return $value;
    }

    /**
     * Get the unique key value for this model.
     *
     * @return mixed
     */
    public function getKey()
    {
        return $this->{$this->getKeyField()};
    }

    /**
     * Get the field name representing the unique key for this model.
     *
     * @return string
     */
    public function getKeyField()
    {
        return $this->_keyField;
    }

    /**
     * Hydrate this instance using the provided data and an optional mask.
     *
     * @param array $data
     * @param bool|array $mask
     */
    public function hydrate(array $data, $mask = false)
    {
        if (!is_array($mask) && false !== $mask) {
            $mask = Collection::getMask($this, [], ['_id']);
        }
        foreach ($data as $key => $value) {
            if (false === $mask || in_array($key, $mask)) {
                $this->set($key, $value);
            }
        }
        if (!$this->isNew()) {
            $this->_dirty = array();
        }
    }

    /**
     * Indicates if this model is new or already persisted.
     *
     * @return bool
     */
    public function isNew()
    {
        return $this->getKey() === null;
    }

    /**
     * Remove this model from the Database.
     *
     * @return bool
     */
    public function remove()
    {
        return (static::collection()->remove([$this->getKeyField() => $this->getKey()]) === 1);
    }

    /**
     * Save this model to the Database.
     *
     * @throws ModelValidationException
     * @return bool
     */
    public function save()
    {
        if ($this->isNew()) {
            return $this->insert();
        } elseif (!empty($this->_dirty)) {
            return $this->patch();
        }
        return false;
    }

    /**
     * Set the value of a property of this model.
     *
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value)
    {
        $this->_dirty[$key] = $key;
        $this->{$key} = $value;
    }

    /**
     * Get an array representation of this model.
     *
     * @param bool|array $mask
     * @param bool $cast
     * @return array
     */
    public function toArray($mask = false, $cast = true)
    {
        $vars = Collection::getPublicVars($this);
        if ($mask === true) {
            $mask = array_keys($vars);
        }
        if ($mask === false || empty($mask)) {
            $array = $vars;
        } else {
            $array = array();
            if (is_array($mask)) {
                foreach ($mask as $key) {
                    if (array_key_exists($key, $vars)) {
                        $varValue = $vars[$key];
                        if ($cast === true) $varValue = $this->collection()->cast($varValue);
                        $array[$key] = $varValue;
                    }
                }
            } else {
                foreach ($vars as $varKey => $varValue) {
                    if ($mask === false || in_array($varKey, $mask)) {
                        if ($cast === true) $varValue = $this->collection()->cast($varValue);
                        $array[$varKey] = $varValue;
                    }
                }
            }
        }
        return $array;
    }

    /**
     * Validate the model data for persistence.
     *
     * @param array $rules Optional rules to add to the validator.
     * @param array $mask An optional mask for patch requests.
     *
     * @return array|bool An array of validation error messages or true.
     */
    public function validate(array $rules = [], array $mask = [])
    {
        $this->_validator = Validator::instance(static::validationRules($rules));
        $all = empty($mask);
        $passed = $this->_validator->check($this->toArray($mask, false), $all);
        return $passed === true ? true : $this->_validator->failures();
    }

    /**
     * Insert this model into the database.
     *
     * @throws \Tacit\Model\ModelValidationException If the insert fails.
     * @return bool Returns true if successful; false otherwise.
     */
    abstract protected function insert();

    /**
     * Patch this model in the database, updating only dirty fields.
     *
     * @throws \Tacit\Model\ModelValidationException If the patch fails.
     * @return bool Returns true if successful; false otherwise.
     */
    abstract protected function patch();
}
