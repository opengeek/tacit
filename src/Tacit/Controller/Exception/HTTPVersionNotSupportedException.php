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


class HTTPVersionNotSupportedException extends RestfulException
{
    protected $status = 505;
    protected $message = "HTTP Version Not Supported";
    protected $description = "The server does not support, or refuses to support, the HTTP protocol version that was used in the request message.";
}
