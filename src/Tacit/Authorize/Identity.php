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

use InvalidArgumentException;

trait Identity
{
    /** @var array */
    private $identities;

    /**
     * Construct a new Identity instance.
     *
     * @param $identitiesFile
     */
    public function __construct($identitiesFile)
    {
        if (!is_readable($identitiesFile)) {
            throw new InvalidArgumentException("No valid tacit.identitiesFile specified");
        }

        $this->identities = require $identitiesFile;
    }

    /**
     * Get defined identities.
     *
     * @return array
     */
    public function getIdentities()
    {
        return $this->identities;
    }

    /**
     * Get the secretKey for a defined clientKey.
     *
     * @param string $clientKey The clientKey to find the secret for.
     *
     * @return string|bool The secretKey or false if a secretKey could not be found for any reason.
     */
    public function getSecretKey($clientKey)
    {
        if (isset($this->identities[$clientKey])) {
            return $this->identities[$clientKey]['secretKey'];
        }

        return false;
    }
}
