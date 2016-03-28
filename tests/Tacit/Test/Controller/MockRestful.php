<?php
/*
 * This file is part of the Tacit package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tacit\Test\Controller;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tacit\Controller\Restful;
use Tacit\Transform\ArrayTransformer;

class MockRestful extends Restful
{
    protected static $allowedMethods = ['OPTIONS', 'HEAD', 'GET', 'POST'];

    public function get(ServerRequestInterface $request, ResponseInterface $response, array $args = [])
    {
        $this->respondWithItem($request, $response, ['message' => 'mock me do you?'], new ArrayTransformer());
    }

    public function post(ServerRequestInterface $request, ResponseInterface $response, array $args = [])
    {
        $parsed = $request->getParsedBody();
        $target = isset($parsed['target']) ? $parsed['target'] : 'undefined';
        $this->respondWithItem($request, $response, ['message' => "mock me do you {$target}?"], new ArrayTransformer());
    }
}
