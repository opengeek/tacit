<?php
/*
 * This file is part of the Tacit package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tacit\Test\Model\RethinkDB;

use DateTime;
use Tacit\Model\RethinkDB\Persistent;
use Tacit\Test\Model\RethinkDB\NestedObject;

class PersistentObject extends Persistent
{
    protected static $collectionName = 'test_objects';
    protected static $validationRules = [
        'name' => 'type:string|notempty',
        'text' => 'type:string',
        'date' => 'classof:\DateTime,null',
        'integer' => 'type:integer',
        'float' => 'type:float',
        'boolean' => 'type:boolean',
        'password' => 'type:string|notempty|minlen:6',
        'arrayOfStrings' => 'type:array',
        'nestedObject' => 'classof:\Tacit\Test\Model\RethinkDB\NestedObject,null',
    ];

    public $id;
    /** @var string */
    public $name;
    /** @var string */
    public $text;
    /** @var \DateTime */
    public $date;
    /** @var int */
    public $integer;
    /** @var float */
    public $float;
    /** @var bool */
    public $boolean = true;
    /** @var string */
    public $password;
    /** @var array[string] */
    public $arrayOfStrings = [];
    /** @var NestedObject */
    public $nestedObject;

    /**
     * Set the value of a property of this model.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function set($key, $value)
    {
        switch ($key) {
            case 'date':
                if (!$value instanceof DateTime) {
                    if (is_string($value)) {
                        $value = new DateTime($value);
                    } elseif (is_int($value)) {
                        $value = new DateTime("@{$value}");
                    }
                }
                break;
            case 'nestedObject':
                if (!$value instanceof NestedObject) {
                    if (is_object($value)) {
                        $value = get_object_vars($value);
                    }

                    if (is_array($value)) {
                        $value = new NestedObject($value);
                    }
                }
                break;
        }

        parent::set($key, $value);
    }

    /**
     * Hydrate this instance using the provided data and an optional mask.
     *
     * @param array|object $data
     * @param bool|array   $mask
     */
    public function hydrate($data, $mask = false)
    {
        parent::hydrate($data, $mask);

        if (isset($data['nestedObject']) && !$this->nestedObject instanceof NestedObject) {
            if (is_object($this->nestedObject)) {
                $this->nestedObject = get_object_vars($this->nestedObject);
            }

            if (is_array($this->nestedObject)) {
                $this->nestedObject = new NestedObject($this->nestedObject);
            }
        }
    }
}
