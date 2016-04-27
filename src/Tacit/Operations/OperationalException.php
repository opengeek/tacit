<?php
/*
 * This file is part of the Tacit package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tacit\Operations;


use Exception;
use Tacit\Controller\Exception\RestfulException;
use Tacit\Controller\Restful;

class OperationalException extends Exception
{
    /** @var string */
    protected $description;
    /** @var string */
    protected $next;
    /** @var array */
    protected $property;

    /**
     * Construct a new OperationalException.
     *
     * @param string         $next
     * @param string         $message
     * @param null           $description
     * @param null           $property
     * @param Exception|null $previous
     */
    public function __construct($next, $message = '', $description = null, $property = null, Exception $previous = null)
    {
        $this->next = $next;
        if (is_string($description)) $this->description = $description;
        if (is_array($property)) $this->property = $property;
        parent::__construct($message, 422, $previous);
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getProperty()
    {
        return $this->property;
    }

    /**
     * @return RestfulException
     */
    public function next()
    {
        return new $this->next($this->getMessage(), $this->getDescription(), $this->getProperty(), $this->getPrevious());
    }
}
