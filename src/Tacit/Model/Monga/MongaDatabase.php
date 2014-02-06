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


use Tacit\Model\Collection;
use Tacit\Model\Database;

class MongaDatabase implements Database
{
    /**
     * @var MongaDatabase
     */
    protected $source;

    public function __construct(\Closure $source)
    {
        $this->source = $source();
    }

    /**
     * Get a specific collection/table from the Database.
     *
     * @param string $name The name of the collection to get.
     *
     * @return Collection A native collection instance wrapped by a Tacit\Model\Collection.
     */
    public function collection($name)
    {
        return new MongaCollection($this, $name);
    }
}
