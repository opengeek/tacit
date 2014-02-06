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


class RequestedRangeNotSatisfiableException extends RestfulException
{
    protected $code = 416;
    protected $message = "Requested Range Not Satisfiable";
    protected $description = "Requested range not satisfiable for this Resource.";
}
