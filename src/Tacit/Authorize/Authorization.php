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


use Slim\Http\Request;
use Tacit\Controller\Restful;
use Tacit\Tacit;

interface Authorization
{
    /**
     * Get the input data from the request to be used for validation.
     *
     * @param \Tacit\Tacit $app
     *
     * @return string A string representation of the input data elements.
     */
    public function getInput(Tacit $app);

    /**
     * Get the signature provided by the client for validation.
     *
     * @param Request $request
     *
     * @return string The signature.
     */
    public function getSignature(Request $request);

    /**
     * Determine if the client has authorization to make the request.
     *
     * @param Restful $controller
     *
     * @return bool Returns true if the client has authorization to make the request.
     */
    public function isValidRequest(Restful $controller);
}
