<?php
/*
 * This file is part of the Tacit package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tacit;


trait Configurable
{
    /**
     * @var array
     */
    private $configuration;

    /**
     * Get a configuration option value or an array of values.
     *
     * @param string|array $key A configuration option key, or an array of keys to retrieve the value(s) for.
     * @param null|array   $options An optional associative array of configuration overrides.
     * @param mixed        $default An optional default value to be used if no value is found.
     * @param bool         $skipEmpty If true, skip empty string values when a key is found.
     *
     * @return mixed A value or an array of values for the requested configuration option key(s).
     */
    public function getOption($key, $options = null, $default = null, $skipEmpty = false)
    {
        $option = $default;
        if (is_array($key)) {
            if (!is_array($option)) {
                $default = $option;
                $option = array();
            }
            foreach ($key as $k) {
                $option[$k] = $this->getOption($k, $options, $default);
            }
        } elseif (is_string($key) && !empty($key)) {
            if (is_array($options) && !empty($options) && array_key_exists($key,
                    $options) && (!$skipEmpty || ($skipEmpty && $options[$key] !== ''))
            ) {
                $option = $options[$key];
            } elseif (is_array($this->configuration) && !empty($this->configuration) && array_key_exists($key,
                    $this->configuration) && (!$skipEmpty || ($skipEmpty && $this->configuration[$key] !== ''))
            ) {
                $option = $this->configuration[$key];
            }
        }

        return $option;
    }

    /**
     * Set a configuration option value.
     *
     * @param string $key A configuration option key.
     * @param mixed  $value The value to set for the specified configuration option key.
     */
    public function setOption($key, $value)
    {
        $this->configuration[$key] = $value;
    }
}
