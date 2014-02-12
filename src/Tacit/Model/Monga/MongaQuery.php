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


use League\Monga\Query\Find;
use Tacit\Model\Query;

/**
 * A Query adaptor for Monga.
 *
 * @package Tacit\Model\Monga
 */
class MongaQuery extends Query
{
    /**
     * @var \League\Monga\Query\Find
     */
    protected $primitive;

    /**
     * Construct a MongaQuery using \League\Monga\Query\Find.
     */
    public function __construct()
    {
        $this->primitive = new Find();
    }
} 
