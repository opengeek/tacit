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


class NotFoundException extends RestfulException
{
    protected $status = 404;
    protected $message = "Not Found";
    protected $description = "Resource not found.";
}
