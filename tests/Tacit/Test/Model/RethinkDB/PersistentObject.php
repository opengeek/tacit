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
use r\Queries\Dates\Time;
use Tacit\Model\RethinkDB\Persistent;

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
    ];

    public $id;
    public $name;
    public $text;
    public $date;
    public $integer;
    public $float;
    public $boolean = true;
    public $password;
    public $arrayOfStrings = [];

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
        }
        parent::set($key, $value);
    }
}
