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


class UnsupportedMediaTypeException extends RestfulException
{
    protected $code = 415;
    protected $message = "Unsupported Media Type";
    protected $description = "The server is refusing to service this request because the entity is in a format not supported by the requested resource for the requested method.";
}
