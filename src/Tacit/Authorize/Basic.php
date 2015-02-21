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


use Tacit\Controller\Exception\ForbiddenException;
use Tacit\Controller\Exception\UnauthorizedException;
use Tacit\Controller\Restful;
use Tacit\Tacit;

class Basic implements Authorization
{
    use Identity;

    /**
     * Get the input data from the request to be used for validation.
     *
     * @param \Tacit\Tacit $app
     *
     * @return string A string representation of the input data elements.
     */
    public function getInput(Tacit $app)
    {
        return '';
    }

    /**
     * Get the signature provided by the client for validation.
     *
     * @param Tacit $app
     *
     * @return string The signature.
     */
    public function getSignature(Tacit $app)
    {
        $signature = ':';
        if (isset($app->environment['PHP_AUTH_USER'])) {
            $signature = $app->environment['PHP_AUTH_USER'] . ':';
            if (isset($app->environment['PHP_AUTH_PW'])) {
                $signature .= $app->environment['PHP_AUTH_PW'];
            }
        }
        return $signature;
    }

    /**
     * Determine if the client has authorization to make the request.
     *
     * @param Restful $controller
     *
     * @throws ForbiddenException
     * @throws UnauthorizedException
     * @return bool Returns true if the client has authorization to make the request.
     */
    public function isValidRequest(Restful $controller)
    {
        $signature = $this->getSignature($controller->getApp());
        if (empty($signature)) {
            throw new UnauthorizedException(
                $controller,
                'Unsigned Request',
                'No valid authorization signature was provided with the request.'
            );
        }
        $exploded = explode(':', $signature, 2);
        if (count($exploded) !== 2) {
            throw new UnauthorizedException(
                $controller,
                'Invalid Signature',
                'The request contains an invalid authorization signature.'
            );
        }

        list($username, $password) = $exploded;

        $secret = $this->getSecretKey($controller->getApp(), $username);

        if ($password !== $secret) {
            throw new ForbiddenException(
                $controller,
                'Unauthorized Signature',
                'The request is not properly signed and has been rejected.'
            );
        }

        return true;
    }
}
