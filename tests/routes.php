<?php
/*
 * This file is part of the Tacit package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

$this->tacit->any('/', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
    return (new \Tacit\Test\Controller\MockRestful($this))->handle($request, $response, $args);
});

$this->tacit->group('/collection', function () {
    $this->any('', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
        return (new \Tacit\Test\Controller\MockRestfulCollection($this))->handle($request, $response, $args);
    })->setName('MockRestfulCollection');
    $this->any('/{_id}', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
        return (new \Tacit\Test\Controller\MockRestfulItem($this))->handle($request, $response, $args);
    })->setName('MockRestfulItem');
});

$this->tacit->group('/monga', function () {
    $this->group('/collection', function () {
//        $this->any('', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
//            return (new \Tacit\Test\Controller\Monga\RestfulCollection($this))->handle($request, $response, $args);
//        })->setName('MongaRestfulCollection');
        $this->any('/{_id}', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
            return (new \Tacit\Test\Controller\Monga\RestfulItem($this))->handle($request, $response, $args);
        })->setName('MongaRestfulItem');
    });
});

$this->tacit->group('/rethinkdb', function () {
    $this->group('/collection', function () {
//        $this->any('', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
//            return (new \Tacit\Test\Controller\RethinkDB\RestfulCollection($this))->handle($request, $response, $args);
//        })->setName('RethinkDBRestfulCollection');
        $this->any('/{id}', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
            return (new \Tacit\Test\Controller\RethinkDB\RestfulItem($this))->handle($request, $response, $args);
        })->setName('RethinkDBRestfulItem');
    });
});

$this->tacit->any('/basic-test', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
    $mockRestful = new \Tacit\Test\Controller\MockRestful($this);
    if ((new \Tacit\Authorize\Basic())->isValidRequest($mockRestful)) {
        return $mockRestful->handle($request, $response, $args);
    } else {
        $response->getBody()->write('Unauthorized Error');
        return $response->withStatus(401);
    }
});

$this->tacit->any('/hmac-test', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
    $mockRestful = new \Tacit\Test\Controller\MockRestful($this);
    if ((new \Tacit\Authorize\HMAC())->isValidRequest($mockRestful)) {
        return $mockRestful->handle($request, $response, $args);
    } else {
        $response->getBody()->write('Unauthorized Error');
        return $response->withStatus(401);
    }
});
