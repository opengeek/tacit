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


class ResourceConflictException extends RestfulException
{
    protected $code = 409;
    protected $message = "Resource Conflict";
    protected $description = "State of the resource does not permit this request.";
}
