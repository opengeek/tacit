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


use Tacit\Model\Exception\ModelValidationException;
use Tacit\Model\Persistent;

abstract class MongaPersistent extends Persistent
{
    /**
     * Name of the field representing the unique key for this model item.
     *
     * @var string
     */
    protected $_keyField = '_id';

    /**
     * The unique identifier for a Mongo model item.
     *
     * @var \MongoId
     */
    public $_id;

    /**
     * Insert this model into the repository.
     *
     * @throws ModelValidationException
     * @return bool
     */
    protected function insert()
    {
        $this->set('created_at', new \MongoDate());
        $validated = $this->validate([], MongaCollection::getMask($this, [], [$this->getKey()]));
        if (true !== $validated) {
            throw new ModelValidationException('model validation failed for new item in collection ' . static::$collectionName, $validated);
        }
        $saved = static::collection($this->getRepository())->insert($this->toArray(MongaCollection::getMask($this, [], [$this->getKeyField()])), ['w' => 1]);
        if ($saved instanceof \MongoId) {
            $this->{$this->getKeyField()} = $saved;
            return true;
        }
        return false;
    }

    /**
     * Patch this model in the repository, updating only dirty fields.
     *
     * @throws ModelValidationException If the patch fails.
     * @return bool Returns true if successful; false otherwise.
     */
    protected function patch()
    {
        $this->set('updated_at', new \MongoDate());
        $validated = $this->validate([], array_keys($this->_dirty));
        if (true !== $validated) {
            throw new ModelValidationException('model validation failed for existing item in collection ' . static::$collectionName, $validated);
        }
        return static::collection($this->getRepository())->update(['$set' => $this->dirty()], array($this->getKeyField() => $this->getKey()));
    }
}
