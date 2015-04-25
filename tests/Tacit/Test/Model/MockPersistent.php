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
use Tacit\Model\Exception\ModelValidationException;
use Tacit\Model\Persistent;

/**
 * Defines a simple hash-based mock Persistent implementation.
 *
 * @package Tacit\Test\Model
 */
class MockPersistent extends Persistent
{
    protected static $collectionName = 'mock_persistent';
    protected static $validationRules = [
        'name' => 'type:string|notempty',
        'text' => 'type:string',
        'integer' => 'type:integer',
        'float' => 'type:float',
        'date' => 'classof:\\DateTime,null',
        'boolean' => 'type:boolean',
        'password' => 'type:string|notempty|minlen:6',
        'arrayOfStrings' => 'type:array',
    ];

    /** @var string */
    public $_id;
    /** @var string */
    public $name;
    /** @var string */
    public $text;
    /** @var \DateTime */
    public $date;
    /** @var integer */
    public $integer;
    /** @var float */
    public $float;
    /** @var bool */
    public $boolean = true;
    /** @var string */
    public $password;
    /** @var string */
    public $arrayOfStrings = [];

    /**
     * Get the unique key field(s) identifying this entity.
     *
     * @return string|int|array[string|int]
     */
    public static function key()
    {
        return '_id';
    }

    /**
     * Insert this model into the repository.
     *
     * @throws ModelValidationException If the insert fails.
     * @return bool Returns true if successful; false otherwise.
     */
    protected function insert()
    {
        return (static::collection($this->getRepository())->insert(Collection::getPublicVars($this)) !== false);
    }

    /**
     * Patch this model in the repository, updating only dirty fields.
     *
     * @throws ModelValidationException If the patch fails.
     * @return bool Returns true if successful; false otherwise.
     */
    protected function patch()
    {
        return (static::collection($this->getRepository())->update(array($this->getKeyField() => $this->getKey()), $this->dirty(false)) !== false);
    }
}
