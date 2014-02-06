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


class PaymentRequiredException extends RestfulException
{
    protected $status = 402;
    protected $message = "Payment Required";
    protected $description = "Payment is required in order to complete this operation.";
}
