<?php
/*
 * This file is part of the Tacit package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tacit\Model\Exception;

/**
 * Represents a validation failure in a Tacit model.
 *
 * @package Tacit\Model\Exception
 */
class ModelValidationException extends ModelException
{
    /**
     * An array of validation error messages grouped by field.
     *
     * @var array[array[string]
     */
    protected $validationMessages;

    /**
     * Construct a new ModelValidationException.
     *
     * @param string     $message The error message.
     * @param array      $messages An array of validation errors grouped by field.
     * @param int        $code The error code associated with the exception.
     * @param \Exception $previous An optional previous Exception that caused
     * this one to be thrown.
     */
    public function __construct($message = "", array $messages = [], $code = 422, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->validationMessages = $messages;
    }

    /**
     * Get an array of field-specific validation error messages.
     *
     * @return array[array[string]] An array of validation messages grouped grouped by field.
     */
    public function getMessages()
    {
        return $this->validationMessages;
    }

    /**
     * Output the ModelValidationException as a string.
     *
     * @return string A formatted string representation of the exception.
     */
    public function __toString()
    {
        $output = [ parent::__toString() ];
        foreach ($this->validationMessages as $field => $errors) {
            foreach ($errors as $error) {
                $output[] .= "[{$field}#{$error['code']}] {$error['message']}";
            }
        }
        return implode("\n", $output);
    }
}
