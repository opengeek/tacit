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


class NotImplementedException extends RestfulException
{
    protected $status = 501;
    protected $message = "Not Implemented";
    protected $description = "Requested HTTP operation not implemented on this resource.";
}
