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


use Closure;

class Rules
{
    /** @var bool */
    protected static $initialized = false;
    /** @var array[Closure] */
    protected static $rules = [];

    /**
     * Create a Rule from a Closure for use by the Validator.
     *
     * @param string  $name
     * @param Closure $closure
     */
    public static function create($name, Closure $closure)
    {
        static::$rules[$name] = $closure;
    }

    /**
     * Get a Rule Closure by name.
     *
     * @param string $name
     *
     * @return Closure
     */
    public function get($name)
    {
        if (!isset(static::$rules[$name])) {
            throw new \InvalidArgumentException("No Rule found with name {$name}");
        }

        return static::$rules[$name];
    }

    public function __construct(array $rules = [])
    {
        if (!static::$initialized) {
            $rules = array_replace(
                [
                    'classof' => function ($field, $value, $args = [], $context = null) {
                        $class = $args[0];
                        $allowNull = isset($args[1]) && $args[1] === 'null';
                        if (!$value instanceof $class) {
                            if (!(null === $value && true === $allowNull)) {
                                throw new ValidationFailedException(sprintf('%1s must be an instance of class %2s',
                                    $field, $class), 422);
                            }
                        }
                    },
                    'email' => function ($field, $value, $args = [], $context = null) {
                        $pattern = '/^([\w\!\#$\%\&\'\*\+\-\/\=\?\^\`{\|\}\~]+\.)*[\w\!\#$\%\&\'\*\+\-\/\=\?\^\`{\|\}\~]+@((((([a-z0-9]{1}[a-z0-9\-]{0,62}[a-z0-9]{1})|[a-z])\.)+[a-z]{2,63})|(\d{1,3}\.){3}\d{1,3}(\:\d{1,5})?)$/i';
                        if (is_array($value)) {
                            foreach ($value as $subVal) {
                                $matchCount = preg_match($pattern, $value);
                                if ($matchCount < 1) {
                                    throw new ValidationFailedException(sprintf('%2s is not a valid email address',
                                        $value), 422);
                                }
                            }
                        } else {
                            $matchCount = preg_match($pattern, $value);
                            if ($matchCount < 1) {
                                throw new ValidationFailedException(sprintf('%2s is not a valid email address', $value),
                                    422);
                            }
                        }
                    },
                    'maxlen' => function ($field, $value, $args = [], $context = null) {
                        $length = (integer)$args[0];
                        if (!is_string($value) || strlen($value) > $length) {
                            throw new ValidationFailedException(sprintf('%1s must be a string with a length no greater than %2s',
                                $field, (string)$length), 422);
                        }
                    },
                    'minlen' => function ($field, $value, $args = [], $context = null) {
                        $length = (integer)$args[0];
                        if (!is_string($value) || strlen($value) < $length) {
                            throw new ValidationFailedException(sprintf('%1s must be a string with a length of at least %2s',
                                $field, (string)$length), 422);
                        }
                    },
                    'notempty' => function ($field, $value, $args = [], $context = null) {
                        if (empty($value) && $value !== '0') {
                            throw new ValidationFailedException(sprintf('%1s cannot have an empty value', $field), 422);
                        }
                    },
                    'required' => function ($field, $value, $args = [], $context = null) {
                        if ($value === null) {
                            throw new ValidationFailedException(sprintf('%1s is a required field', $field), 422);
                        }
                    },
                    'strlen' => function ($field, $value, $args = [], $context = null) {
                        $length = (integer)$args[0];
                        if (!is_string($value) || strlen($value) !== $length) {
                            throw new ValidationFailedException(sprintf('%1s must be a string with a length of %2s',
                                $field, (string)$length), 422);
                        }
                    },
                    'type' => function ($field, $value, $args = [], $context = null) {
                        $type = strtolower($args[0]);
                        if ($type === 'float') {
                            $type = 'double';
                        }
                        $typeOf = gettype($value);
                        $allowNull = isset($args[1]) && $args[1] === 'null';
                        if ($typeOf !== $type) {
                            if (!(null === $value && true === $allowNull)) {
                                throw new ValidationFailedException(sprintf('%1s must have a value of type %2s', $field,
                                    $type), 422);
                            }
                        }
                    },
                    'url' => function ($field, $value, $args = [], $context = null) {
                        $allowNull = isset($args[0]) && $args[0] === 'null';
                        if (!(null === $value && true === $allowNull)) {
                            if (!filter_var($value, FILTER_VALIDATE_URL)) {
                                throw new ValidationFailedException(sprintf('%1s must be a valid URL', $field), 422);
                            }
                        }
                    }
                ],
                $rules
            );

            static::$initialized = true;
        }

        foreach ($rules as $ruleName => $rule) {
            static::create($ruleName, $rule);
        }
    }
}
