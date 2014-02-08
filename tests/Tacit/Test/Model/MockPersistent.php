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

class MockPersistent
{
    use Persistent

    /**
     * The name of a field representing the unique identifier for this model item.
     *
     * @var string
     */
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
