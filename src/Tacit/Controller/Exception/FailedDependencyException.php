<?php
/*
 * This file is part of the Tacit package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tacit\Controller\Exception;


class FailedDependencyException extends RestfulException
{
    protected $status = 424;
    protected $message = "Failed Dependency";
    protected $description = "The operation failed because a dependent operation failed.";
}
