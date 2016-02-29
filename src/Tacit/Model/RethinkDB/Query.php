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


use r\Queries\Selecting\Filter;
use r\ValuedQuery\ValuedQuery;

class Query extends \Tacit\Model\Query
{
    /**
     * @var ValuedQuery
     */
    protected $primitive;

    public function __construct(ValuedQuery $query, $predicate, $default = null)
    {
        $this->primitive = new Filter($query, $predicate, $default);
    }
}
