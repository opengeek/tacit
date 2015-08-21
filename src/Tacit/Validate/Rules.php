<?php
/*
 * This file is part of the Tacit package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tacit\Validate;


class Rules
{
    protected static $rules = array();

    public static function __callStatic($name, $arguments)
    {
        if (isset(self::$rules[$name]) && is_callable(self::$rules[$name])) {
            $callable = self::$rules[$name];
            return $callable(array_shift($arguments), array_shift($arguments), array_shift($arguments));
        }
        throw new \BadMethodCallException(sprintf('%1s is not a valid Rule', $name), 500);
    }

    public static function create($name, $callable)
    {
        self::$rules[$name] = $callable;
    }

    public static function classof($field, $value, $args)
    {
        $class = $args[0];
        $allowNull = isset($args[1]) && $args[1] === 'null';
        if (!$value instanceof $class) {
            if (!(null === $value && true === $allowNull)) {
                throw new ValidationFailedException(sprintf('%1s must be an instance of class %2s', $field, $class), 422);
            }
        }
    }

    public static function email($field, $value, $args = [])
    {
        if (is_array($value)) {
            foreach ($value as $subVal) {
                self::email($field, $subVal, $args);
            }
        } else {
            $pattern = '/^([\w\!\#$\%\&\'\*\+\-\/\=\?\^\`{\|\}\~]+\.)*[\w\!\#$\%\&\'\*\+\-\/\=\?\^\`{\|\}\~]+@((((([a-z0-9]{1}[a-z0-9\-]{0,62}[a-z0-9]{1})|[a-z])\.)+[a-z]{2,6})|(\d{1,3}\.){3}\d{1,3}(\:\d{1,5})?)$/i';
            $matchCount = preg_match($pattern, $value);
            if ($matchCount < 1) {
                throw new ValidationFailedException(sprintf('%2s is not a valid email address', $value), 422);
            }
        }
    }

    public static function maxlen($field, $value, $args)
    {
        $length = (integer)$args[0];
        if (!is_string($value) || strlen($value) > $length) {
            throw new ValidationFailedException(sprintf('%1s must be a string with a length no greater than %2s', $field, (string)$length), 422);
        }
    }

    public static function minlen($field, $value, $args)
    {
        $length = (integer)$args[0];
        if (!is_string($value) || strlen($value) < $length) {
            throw new ValidationFailedException(sprintf('%1s must be a string with a length of at least %2s', $field, (string)$length), 422);
        }
    }

    public static function notempty($field, $value, $args = [])
    {
        if (empty($value) && $value !== '0') {
            throw new ValidationFailedException(sprintf('%1s cannot have an empty value', $field), 422);
        }
    }

    public static function required($field, $value, $args = [])
    {
        if ($value === null) {
            throw new ValidationFailedException(sprintf('%1s is a required field', $field), 422);
        }
    }

    public static function strlen($field, $value, $args)
    {
        $length = (integer)$args[0];
        if (!is_string($value) || strlen($value) !== $length) {
            throw new ValidationFailedException(sprintf('%1s must be a string with a length of %2s', $field, (string)$length), 422);
        }
    }

    public static function type($field, $value, $args)
    {
        $type = strtolower($args[0]);
        if ($type === 'float') {
            $type = 'double';
        }
        $typeOf = gettype($value);
        $allowNull = isset($args[1]) && $args[1] === 'null';
        if ($typeOf !== $type) {
            if (!(null === $value && true === $allowNull)) {
                throw new ValidationFailedException(sprintf('%1s must have a value of type %2s', $field, $type), 422);
            }
        }
    }

    public static function url($field, $value, $args)
    {
        $allowNull = isset($args[0]) && $args[0] === 'null';
        if (!(null === $value && true === $allowNull)) {
            if (!filter_var($value, FILTER_VALIDATE_URL)) {
                throw new ValidationFailedException(sprintf('%1s must be a valid URL', $field), 422);
            }
        }
    }
}
