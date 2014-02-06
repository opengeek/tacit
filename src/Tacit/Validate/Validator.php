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


class Validator
{
    /** @var array */
    protected $ruleSet;
    /** @var array */
    protected $failures;
    /** @var bool */
    protected $checked = false;
    /** @var bool */
    protected $failed = false;

    public static function instance(array $rules = array())
    {
        return new static($rules);
    }

    public function check($input, $all = false)
    {
        $data = new \ArrayIterator($input);
        foreach ($this->ruleSet as $field => $rules) {
            if ($all || $data->offsetExists($field)) {
                $fieldValue = $data->offsetExists($field)
                    ? $data->offsetGet($field)
                    : null;
                foreach (explode('|', $rules) as $ruleDef) {
                    $ruleExploded = explode(':', $ruleDef, 2);
                    $rule = $ruleExploded[0];
                    $ruleArgs = isset($ruleExploded[1]) ? explode(',', $ruleExploded[1]) : [];
                    try {
                        Rules::$rule($field, $fieldValue, $ruleArgs);
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

    public function reset()
    {
        $this->failures = [];
        $this->checked = false;
    }

    public function failed()
    {
        if ($this->checked) {
            return $this->failed;
        } else {
            throw new RulesNotCheckedException();
        }
    }

    public function failures()
    {
        if ($this->checked) {
            return $this->failures;
        } else {
            throw new RulesNotCheckedException();
        }
    }

    protected function __construct(array $rules)
    {
        $this->ruleSet = $rules;
        $this->failures = [];
    }

    protected function addFailure($field, $message, $code = E_USER_NOTICE)
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
