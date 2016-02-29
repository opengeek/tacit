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


use r\Queries\Dbs\Db;

final class Connection
{
    /**
     * The native RethinkDB connection.
     *
     * @var \r\Connection
     */
    protected $handle;

    /**
     * The native RethinkDB database object.
     *
     * @var Db
     */
    protected $db;

    public function __construct(\r\Connection $handle, Db $db)
    {
        $this->handle = $handle;
        $this->db = $db;
    }

    public function getHandle()
    {
        return $this->handle;
    }

    public function getDb()
    {
        return $this->db;
    }
}
