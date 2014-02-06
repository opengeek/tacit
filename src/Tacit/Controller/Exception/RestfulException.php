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

use Tacit\Controller\Restful;

/**
 * Represents an exception occurring during a RESTful request.
 *
 * @package Tacit\Controller\Exception
 */
class RestfulException extends \Exception
{
    protected $code;
    protected $description;
    protected $property;
    protected $status = 500;

    public function __construct(Restful $controller, $message = 'Server Error', $property = '', $description = 'Internal Server Error.', $code = 0, $previous = null)
    {
        if ($code <= 0) {
            $code = (int)"{$this->status}0";
        }
        parent::__construct($message, $code, $previous);
        $this->property = $property;
        $this->description = $description;

        $controller->respondWithError($this);
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getProperty()
    {
        return $this->property;
    }

    public function getStatus()
    {
        return $this->status;
    }
}
