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


class PreconditionFailedException extends RestfulException
{
    protected $code = 412;
    protected $message = "Precondition Failed";
    protected $description = "Operation not completed because preconditions were not met.";
}
