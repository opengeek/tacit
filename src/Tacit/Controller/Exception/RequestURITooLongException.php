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


class RequestURITooLongException extends RestfulException
{
    protected $status = 414;
    protected $message = "Request URI Too Long";
    protected $description = "The URI has more than 2000 characters.";
}
