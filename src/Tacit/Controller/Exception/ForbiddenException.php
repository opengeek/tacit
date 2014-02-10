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


class ForbiddenException extends RestfulException
{
    protected $status = 403;
    protected $message = "Forbidden";
    protected $description = "You do not have authority to access this Resource.";
}
