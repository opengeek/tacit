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

use Interop\Container\ContainerInterface;

trait Identity
{
    private static $identities;

    public static function identities(ContainerInterface $container)
    {
        if (!is_array(static::$identities) && isset($container->get('settings')['tacit.identitiesFile'])) {
            $identitiesFile = $container->get('settings')['tacit.identitiesFile'];
            if (is_readable($identitiesFile)) {
                static::$identities = include $identitiesFile;
            }
        }
        return static::$identities;
    }

    public function getSecretKey(ContainerInterface $container, $clientKey)
    {
        $identities = static::identities($container);
        if (isset($identities[$clientKey])) {
            return $identities[$clientKey]['secretKey'];
        }
        return false;
    }
}
