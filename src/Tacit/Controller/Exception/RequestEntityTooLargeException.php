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


class RequestEntityTooLargeException extends RestfulException
{
    protected $code = 413;
    protected $message = "Request Entity Too Large";
    protected $description = "The representation was too large for the server to handle.";
}
