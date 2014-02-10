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


class InsufficientStorageException extends RestfulException
{
    protected $status = 507;
    protected $message = "Insufficient Storage";
    protected $description = "Operation cannot be performed on the resource because the server is unable to store the representation needed to successfully complete the request.";
}
