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

    /** @var string */
    public $_id;
    /** @var string */
    public $name;
    /** @var \DateTime */
    public $date;
    /** @var integer */
    public $int;
    /** @var float */
    public $float;

    protected $_keyField = '_id';

    /**
     * Insert this model into the repository.
     *
     * @throws ModelValidationException If the insert fails.
     * @return bool Returns true if successful; false otherwise.
     */
    protected function insert()
    {
        return ($this->collection()->insert(Collection::getPublicVars($this)) !== false);
    }

    /**
     * Patch this model in the repository, updating only dirty fields.
     *
     * @throws ModelValidationException If the patch fails.
     * @return bool Returns true if successful; false otherwise.
     */
    protected function patch()
    {
        return ($this->collection()->update(array($this->getKeyField() => $this->getKey()), $this->dirty()) !== false);
    }
}
