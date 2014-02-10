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


class NotAcceptableException extends RestfulException
{
    protected $status = 406;
    protected $message = "Not Acceptable";
    protected $description = "Requested representation not available for this Resource.";
}
