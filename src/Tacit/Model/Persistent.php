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


use Exception;
use Interop\Container\ContainerInterface;
use Tacit\Model\Exception\ModelException;
use Tacit\Model\Exception\ModelInsertException;
use Tacit\Model\Exception\ModelValidationException;
use Tacit\Validate\Validator;

/**
 * Defines the behaviors and properties of a Persistent model object.
 *
 * @package Tacit\Model
 */
abstract class Persistent
{
    /**
     * @var string The name of the native collection container.
     */
    protected static $collectionName;

    /**
     * @var Collection A Collection wraps a document/row container in a Repository.
     */
    protected static $collection;

    /**
     * @var array An array of Rules to use when validating objects if this class.
     */
    protected static $validationRules = [];

    /**
     * @var ContainerInterface A DI container containing dependencies for the model.
     */
    protected $_container;

    /**
     * @var Repository A Repository represents a collection container or database.
     */
    protected $_repository;

    /**
     * An array containing keys of fields that are dirty for this model item.
     *
     * @var array
     */
    protected $_dirty = [];

    /**
     * A Validator instance for this model item.
     *
     * @var Validator
     */
    protected $_validator;

    /**
     * Get the Collection from the Repository.
     *
     * @param Repository $repository A specific Repository to get the Collection from.
     * @param array      $options    An array of options to configure the Collection with.
     *
     * @return Collection The Collection from the Repository.
     */
    public static function collection(Repository $repository, array $options = [])
    {
        static::$collection = $repository->collection(static::collectionName(), $options);

        return static::$collection;
    }

    /**
     * Get the name of the Collection in the Repository.
     *
     * @return string
     */
    public static function collectionName()
    {
        return static::$collectionName;
    }

    /**
     * Create an instance of this model class in the persistence provider.
     *
     * @param Repository $repository
     * @param array      $data The data to create the Persistent object with.
     *
     * @return null|Persistent
     * @throws ModelInsertException
     * @throws ModelValidationException
     */
    public static function create(Repository $repository, array $data = [])
    {
        try {
            /** @var Persistent $instance */
            $instance = new static($repository);
            $instance->fromArray($data, true);
            $result = $instance->save();
        } catch (ModelValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new ModelInsertException("Error creating item in collection " . static::collectionName(), $e->getCode(), $e);
        }
        if (false === $result) {
            throw new ModelInsertException("Error creating item in collection " . static::collectionName());
        }
        return $instance;
    }

    /**
     * Create an instance of this model class.
     *
     * @param Repository $repository
     * @param array      $data The data to initialize the Persistent object with.
     *
     * @return null|Persistent An instance of the Persistent object or null.
     */
    public static function instance(Repository $repository, array $data = [])
    {
        /** @var Persistent $instance */
        $instance = new static($repository);
        $instance->hydrate($data, true);
        return $instance;
    }

    /**
     * Find objects within this Collection meeting the specified criteria.
     *
     * @param Repository     $repository
     * @param array|\Closure $criteria
     * @param array          $fields      An array of fields to be returned from the object.
     *                                    All fields are returned if not provided.
     * @param array          $options     An array of options to configure the Collection with.
     *
     * @return array [static] An array of Persistent objects, possibly empty.
     */
    public static function find(Repository $repository, $criteria = [], array $fields = [], array $options = [])
    {
        $collection = [];
        $models = static::collection($repository, $options)->find($criteria, $fields);
        if ($models) {
            foreach ($models as $id => $model) {
                /** @var Persistent $object */
                $object = new static($repository);
                $object->hydrate($model);
                $key = $object->getKey();
                $collection[is_scalar($key) ? $key : $id] = $object;
            }
        }
        return $collection;
    }

    /**
     * Count objects within this Collection meeting the specified criteria.
     *
     * @param Repository     $repository
     * @param array|\Closure $criteria The criteria for limiting the objects to count.
     * @param array          $options  An array of options to configure the Collection with.
     *
     * @return int The number of objects in the Collection.
     */
    public static function count(Repository $repository, $criteria = [], array $options = [])
    {
        return static::collection($repository, $options)->count($criteria);
    }

    /**
     * Find a single object within this Collection meeting the specified criteria.
     *
     * @param Repository     $repository
     * @param array|\Closure $criteria     The criteria for selecting the object from the Collection.
     * @param array          $fields       An array of fields to be returned from the object.
     *                                     All fields are returned if not provided.
     * @param array          $options      An array of options to configure the Collection with.
     *
     * @return null|Persistent The Persistent object meeting the criteria or null.
     */
    public static function findOne(Repository $repository, $criteria, array $fields = [], array $options = [])
    {
        $instance = null;
        if ($model = static::collection($repository, $options)->findOne($criteria, $fields)) {
            /** @var Persistent $instance */
            $instance = new static($repository);
            $instance->hydrate($model);
        }
        return $instance;
    }

    /**
     * Update one or more objects in this Collection meeting the specified criteria.
     *
     * @param Repository     $repository
     * @param array|\Closure $criteria The criteria for selecting the objects to update.
     * @param array          $fields   An associative array of fields to update in the object(s).
     * @param array          $options  An array of options for the update.
     *
     * @return bool|int Returns the number of objects updated or false on failure.
     */
    public static function update(Repository $repository, $criteria, array $fields, array $options = ['w' => 1])
    {
        return static::collection($repository)->update($criteria, $fields, $options);
    }

    /**
     * Get the unique key field(s) identifying this entity.
     *
     * @return string|int|array[string|int]
     */
    public static function key()
    {
        return 'id';
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
        return array_replace_recursive(static::$validationRules, $rules);
    }

    /**
     * Get a new instance of a Persistent object.
     *
     * @param Repository $repository
     *
     * @throws ModelException If no valid Repository is available from the DI container.
     */
    public function __construct(Repository $repository)
    {
        $this->setRepository($repository);
    }

    /**
     * Get the Repository for this Persistent instance.
     *
     * @return Repository|null This instance's Repository or null if not set.
     */
    public function getRepository()
    {
        return $this->_repository;
    }

    /**
     * Set this Persistent instance's Repository
     *
     * @param Repository $repository A Repository to set for this instance.
     */
    public function setRepository(Repository $repository)
    {
        $this->_repository = $repository;
    }

    /**
     * Get an array of properties with dirty values.
     *
     * @param bool $cast
     *
     * @return array
     */
    public function dirty($cast = true)
    {
        return $this->toArray($this->_dirty, $cast);
    }

    /**
     * Set the properties of this instance using the provided data and an optional mask.
     *
     * @param array $data
     * @param bool|array $mask
     */
    public function fromArray(array $data, $mask = false)
    {
        if (!is_array($mask) && false !== $mask) {
            $mask = Collection::getMask($this, [], ['_id']);
        }
        foreach ($data as $key => $value) {
            if (false === $mask || in_array($key, $mask)) {
                $this->set($key, $value);
            }
        }
        if ($this->isNew()) {
            $this->_dirty = [];
        }
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
     * @return string|array[string]
     */
    public function getKeyField()
    {
        return static::key();
    }

    /**
     * Hydrate this instance using the provided data and an optional mask.
     *
     * @param array|object $data
     * @param bool|array $mask
     */
    public function hydrate($data, $mask = false)
    {
        if (!is_array($mask) && false !== $mask) {
            $mask = Collection::getMask($this, [], ['_id']);
        }
        foreach ($data as $key => $value) {
            if (false === $mask || in_array($key, $mask)) {
                $this->{$key} = $value;
                $this->_dirty[$key] = $key;
            }
        }
        if (!$this->isNew()) {
            $this->_dirty = [];
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
     * Remove this model from the Repository.
     *
     * @return bool
     */
    public function remove()
    {
        return (static::collection($this->getRepository())->remove([$this->getKeyField() => $this->getKey()]) !== false);
    }

    /**
     * Save this model to the Repository.
     *
     * @throws ModelException
     * @return bool
     */
    public function save()
    {
        if ($this->isNew()) {
            try {
                return $this->insert();
            } catch (ModelException $e) {
                throw $e;
            } catch (Exception $e) {
                throw new ModelException('Error creating item in collection: ' . $e->getMessage(), $e->getCode(), $e);
            }
        } elseif (!empty($this->_dirty)) {
            try {
                return $this->patch();
            } catch (ModelException $e) {
                throw $e;
            } catch (Exception $e) {
                throw new ModelException('Error updating item in collection: ' . $e->getMessage(), $e->getCode(), $e);
            }
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
        $array = [];
        if (is_array($mask) && !empty($mask)) {
            foreach ($mask as $key) {
                if (array_key_exists($key, $vars)) {
                    $varValue = $vars[$key];
                    if ($cast === true) $varValue = static::collection($this->getRepository())->cast($varValue);
                    $array[$key] = $varValue;
                }
            }
        } else {
            foreach ($vars as $varKey => $varValue) {
                if ($mask === false || in_array($varKey, $mask)) {
                    if ($cast === true) $varValue = static::collection($this->getRepository())->cast($varValue);
                    $array[$varKey] = $varValue;
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
        $passed = $this->_validator->check($this->toArray($mask, false), $all, $this);
        return $passed === true ? true : $this->_validator->failures();
    }

    /**
     * Insert this model into the repository.
     *
     * @throws ModelValidationException If the insert fails.
     * @return bool Returns true if successful; false otherwise.
     */
    abstract protected function insert();

    /**
     * Patch this model in the repository, updating only dirty fields.
     *
     * @throws ModelValidationException If the patch fails.
     * @return bool Returns true if successful; false otherwise.
     */
    abstract protected function patch();
}
