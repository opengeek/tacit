<?php
/*
 * This file is part of the Tacit package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tacit\Model\RethinkDB;


use Tacit\Model\Exception\ModelValidationException;

class Persistent extends \Tacit\Model\Persistent
{
    /**
     * The unique identifier for a RethinkDB model item.
     *
     * @var string
     */
    public $id;

    /**
     * Insert this model into the repository.
     *
     * @throws ModelValidationException If the insert fails.
     * @return bool Returns true if successful; false otherwise.
     */
    protected function insert()
    {
        $validated = $this->validate([], Collection::getMask($this, [], [$this->getKey()]));
        if (true !== $validated) {
            throw new ModelValidationException('model validation failed for new item in collection ' . static::$collectionName, $validated);
        }
        $saved = static::collection($this->getRepository())->insert($this->toArray(Collection::getMask($this, [], [$this->getKeyField()]), false));
        if ($saved !== false) {
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
        $validated = $this->validate([], array_keys($this->_dirty));
        if (true !== $validated) {
            throw new ModelValidationException('model validation failed for existing item in collection ' . static::$collectionName, $validated);
        }
        return static::collection($this->getRepository())->update([$this->getKeyField() => $this->getKey()], $this->dirty(false));
    }
}
