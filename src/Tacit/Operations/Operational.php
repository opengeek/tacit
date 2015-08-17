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


use Tacit\Tacit;

trait Operational
{
    /** @var Tacit */
    public    $app;

    public function __construct(Tacit &$app)
    {
        $this->app =& $app;
    }

    public function property($key, array $data = [], $default = null)
    {
        $value = $default;
        if (array_key_exists($key, $data)) {
            $value = $data[$key];
        } elseif (array_key_exists($key, $this->defaultProperties())) {
            $value = $this->defaultProperties()[$key];
        }
        return $value;
    }

    protected function defaultProperties()
    {
        return [];
    }
}
