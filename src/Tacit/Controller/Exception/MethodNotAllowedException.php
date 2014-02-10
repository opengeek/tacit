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


class MethodNotAllowedException extends RestfulException
{
    protected $status = 405;
    protected $message = "Method Not Allowed";
    protected $description = "Method not allowed on this Resource.";
}
