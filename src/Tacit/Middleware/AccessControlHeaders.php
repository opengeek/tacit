<?php
/*
 * This file is part of the Tacit package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tacit\Middleware;


use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Tacit\Controller\Restful;

class AccessControlHeaders
{
    /**
     * @param Request  $request
     * @param Response $response
     * @param callable $next
     *
     * @return Response
     */
    public function __invoke($request, $response, $next)
    {
        /** @var Response $response */
        $response = $next($request, $response);
        $response = $response->withHeader('Access-Control-Allow-Origin', $request->getAttribute(Restful::ALLOWED_ORIGINS, ['*']))
            ->withHeader('Access-Control-Allow-Headers', 'Origin,Content-Type,Accept,Authorization')
            ->withHeader('Access-Control-Allow-Methods', implode(',', $request->getAttribute(Restful::ALLOWED_METHODS, ['OPTIONS','HEAD','GET','POST','PUT','PATCH','DELETE'])));

        return $response;
    }
}
