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
use Tacit\Model\Persistent;

/**
 * A class to handle validation of data.
 *
 * @package Tacit\Validate
 */
class Validator
{
    /**
     * An instance of the set of Closures which implement the Rules.
     *
     * @var Rules
     */
    protected $rules;

    /**
     * A set of rules for the this instance.
     *
     * @var array
     */
    protected $ruleSet;

    /**
     * An array of failures for this instance.
     *
     * @var array
     */
    protected $failures;

    /**
     * Indicates if this instance has validated data.
     *
     * @var bool
     */
    protected $checked = false;

    /**
     * Indicates if any rules failed after they have been checked.
     *
     * @var bool
     */
    protected $failed = false;

    /**
     * Get an instance of a Validator with a specific set of Rules.
     *
     * @param array $rules A set of Rules for the Validator instance.
     *
     * @return static An instance of the Validator class.
     */
    public static function instance(array $rules = [])
    {
        return new static($rules);
    }

    /**
     * Check input against the Rules configured for this Validator.
     *
     * @param array|object|Persistent $input The input data to check.
     * @param bool $all If true, all rules will be checked even if the field
     * does not exist in the input data.
     * @param array|object $context Contextual data that can be used by the Rules.
     *
     * @return bool True if all Rules pass, false otherwise.
     */
    public function check($input, $all = false, $context = [])
    {
        $data = new \ArrayIterator($input);
        $contextIterator = new \ArrayIterator(
            $context instanceof Persistent
                ? $context->toArray(false, false)
                : $context
        );
        foreach ($this->ruleSet as $field => $rules) {
            if ($all || $data->offsetExists($field)) {
                $fieldValue = $data->offsetExists($field)
                    ? $data->offsetGet($field)
                    : null;
                foreach (explode('|', $rules) as $ruleDef) {
                    $ruleExploded = explode(':', $ruleDef, 2);
                    $rule = $ruleExploded[0];
                    $ruleArgs = isset($ruleExploded[1]) ? explode(',', $ruleExploded[1]) : [];
                    $ruleClosure = $this->rules->get($rule);
                    if (is_object($context)) {
                        $ruleClosure = $ruleClosure->bindTo($context, $context);
                    }
                    try {
                        $ruleClosure($field, $fieldValue, $ruleArgs, $contextIterator);
                    } catch (ValidationFailedException $failure) {
                        $this->addFailure($field, $failure->getMessage(), $failure->getCode());
                        $this->failed = true;
                    }
                }
            }
        }
        $this->checked = true;
        return $this->failed === false;
    }

    /**
     * Reset this instance, clearing failures.
     */
    public function reset()
    {
        $this->failures = [];
        $this->checked = false;
    }

    /**
     * Determine if any Rules failed after being checked.
     *
     * @throws RulesNotCheckedException If no input data has been checked yet.
     * @return bool Returns true if any checked Rules failed, or false if all passed.
     */
    public function failed()
    {
        if ($this->checked) {
            return $this->failed;
        } else {
            throw new RulesNotCheckedException();
        }
    }

    /**
     * Return details about all failed Rules.
     *
     * @throws RulesNotCheckedException If no input data has been checked yet.
     * @return array A multi-dimensional array of failures grouped by field.
     */
    public function failures()
    {
        if ($this->checked) {
            return $this->failures;
        } else {
            throw new RulesNotCheckedException();
        }
    }

    /**
     * Constructs a new Validator instance.
     *
     * @param array $rules A set of Rules for this instance.
     */
    protected function __construct(array $rules)
    {
        $this->rules = new Rules();
        $this->ruleSet = $rules;
        $this->failures = [];
    }

    /**
     * Add a failure to this Validator instance.
     *
     * @param string $field The name of the field the Rule was applied to.
     * @param string $message The failure message for the Rule.
     * @param int    $code An error code associated with the failure.
     */
    protected function addFailure($field, $message, $code = 422)
    {
        if (!isset($this->failures[$field])) {
            $this->failures[$field] = [];
        }
        $this->failures[$field][] = [
            'code' => $code,
            'field' => $field,
            'message' => $message
        ];
    }
}
