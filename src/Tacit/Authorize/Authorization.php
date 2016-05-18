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
use Tacit\Controller\Exception\RestfulException;
use Tacit\Controller\Exception\UnauthorizedException;
use Tacit\Controller\Restful;

interface Authorization
{
    /**
     * Get the input data from the request to be used for validation.
     *
     * @param ServerRequestInterface $request
     *
     * @return string A string representation of the input data elements.
     */
    public function getInput(ServerRequestInterface $request);

    /**
     * Get the signature provided by the client for validation.
     *
     * @param ServerRequestInterface $request
     *
     * @return string The signature.
     */
    public function getSignature(ServerRequestInterface $request);

    /**
     * Determine if the client has authorization to make the request.
     *
     * @param ServerRequestInterface $request
     *
     * @return bool Returns true if the client has authorization to make the request.
     */
    public function isValidRequest(ServerRequestInterface $request);
}
