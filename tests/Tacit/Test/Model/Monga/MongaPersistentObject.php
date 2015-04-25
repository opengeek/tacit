<?php
/*
 * This file is part of the Tacit package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tacit\Test\Model\Monga;


use Tacit\Model\Monga\MongaPersistent;

class MongaPersistentObject extends MongaPersistent
{
    protected static $collectionName = 'test_objects';
    protected static $validationRules = [
        'name' => 'type:string|notempty',
        'text' => 'type:string',
        'integer' => 'type:integer',
        'float' => 'type:float',
        'date' => 'classof:\\MongoDate,null',
        'boolean' => 'type:boolean',
        'password' => 'type:string|notempty|minlen:6',
        'arrayOfStrings' => 'type:array',
    ];

    public $name;
    public $text;
    public $integer;
    public $float;
    public $date;
    public $boolean = true;
    public $password;
    public $arrayOfStrings = [];

    public function set($key, $value)
    {
        switch ($key) {
            case 'date':
                if (!$value instanceof \MongoDate) {
                    if ($value instanceof \DateTime) {
                        $value = new \MongoDate($value->getTimestamp());
                    } elseif (is_string($value)) {
                        $value = new \MongoDate(strtotime($value));
                    } elseif (is_int($value)) {
                        $value = new \MongoDate($value);
                    }
                }
                break;
        }
        parent::set($key, $value);
    }
}
