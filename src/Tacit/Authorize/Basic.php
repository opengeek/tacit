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


use Psr\Http\Message\ServerRequestInterface;
use Tacit\Controller\Exception\ForbiddenException;
use Tacit\Controller\Exception\UnauthorizedException;

class Basic implements Authorization
{
    use Identity;

    /**
     * Get the input data from the request to be used for validation.
     *
     * @param ServerRequestInterface $request
     *
     * @return string A string representation of the input data elements.
     */
    public function getInput(ServerRequestInterface $request)
    {
        return '';
    }

    /**
     * Get the signature provided by the client for validation.
     *
     * @param ServerRequestInterface $request
     *
     * @return string The signature.
     */
    public function getSignature(ServerRequestInterface $request)
    {
        $signature = '';

        if ($request->hasHeader('PHP_AUTH_USER')) {
            $signature = $request->getHeader('PHP_AUTH_USER')[0];
            if ($request->hasHeader('PHP_AUTH_PW')) {
                $signature .= ':' . $request->getHeader('PHP_AUTH_PW')[0];
            }
        }

        return $signature;
    }

    /**
     * Determine if the client has authorization to make the request.
     *
     * @param ServerRequestInterface $request
     *
     * @return bool Returns true if the client has authorization to make the request.
     * @throws ForbiddenException If provided credentials do not grant authority to access the resource.
     * @throws UnauthorizedException If no credentials are provided and the resource requires them for access.
     */
    public function isValidRequest(ServerRequestInterface $request)
    {
        $signature = $this->getSignature($request);
        if (empty($signature)) {
            throw new UnauthorizedException(
                'Unsigned Request',
                'No valid authorization signature was provided with the request.'
            );
        }
        $exploded = explode(':', $signature, 2);
        if (count($exploded) !== 2) {
            throw new UnauthorizedException(
                'Invalid Signature',
                'The request contains an invalid authorization signature.'
            );
        }

        list($username, $password) = $exploded;

        $secret = $this->getSecretKey($username);

        if ($password !== $secret) {
            throw new ForbiddenException(
                'Unauthorized Signature',
                'The request is not properly signed and has been rejected.'
            );
        }

        return true;
    }
}
