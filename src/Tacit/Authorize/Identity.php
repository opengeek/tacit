<?php
/*
 * This file is part of the Tacit package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tacit\Authorize;


use Tacit\Tacit;

trait Identity
{
    private static $identities;

    public static function identities(Tacit $app)
    {
        if (!is_array(static::$identities)) {
            $identitiesFile = $app->config('tacit.identitiesFile');
            if (is_readable($identitiesFile)) {
                static::$identities = include $identitiesFile;
            }
        }
        return static::$identities;
    }

    public function getSecretKey($app, $clientKey)
    {
        $identities = static::identities($app);
        if (isset($identities[$clientKey])) {
            return $identities[$clientKey]['secretKey'];
        }
        return false;
    }
}
