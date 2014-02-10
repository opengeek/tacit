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


class UnacceptableEntityException extends RestfulException
{
    protected $status = 422;
    protected $message = "Unprocessable Entity";
    protected $description = "The request was received but could not be processed.";
}
