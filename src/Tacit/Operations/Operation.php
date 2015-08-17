<?php
/*
 * This file is part of the Tacit package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tacit\Operations;

/**
 * Defines the behavior of an Operation.
 *
 * @package Tacit\Operations
 */
interface Operation
{
    /**
     * Execute the operation.
     *
     * @param array   $data An array of properties provided for the operation.
     *
     * @throws OperationalException When an error occurs during the operation.
     * @return mixed
     */
    public function execute(array $data);
}
