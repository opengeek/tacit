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


class LengthRequiredException extends RestfulException
{
    protected $code = 411;
    protected $message = "Length Required";
    protected $description = "The server needs to know the size of the entity body and it should be specified in the Content Length header.";
}
