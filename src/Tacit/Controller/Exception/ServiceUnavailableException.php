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


class ServiceUnavailableException extends RestfulException
{
    protected $status = 503;
    protected $message = "Service Unavailable";
    protected $description = "The server is currently unable to handle the request due to a temporary overloading or maintenance of the server.";
}
