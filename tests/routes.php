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
    return (new \Tacit\Test\Controller\MockRestful($this->settings, $this->router, $this->fractal))
        ->handle($request, $response, $args);
});

$this->tacit->group('/collection', function () {
    $this->any('[/]', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
        return (new \Tacit\Test\Controller\MockRestfulCollection($this->settings, $this->router, $this->fractal, $this->repository))->handle($request, $response, $args);
    })->setName('MockRestfulCollection');
    $this->any('/{id}[/]', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
        $args['_id'] = $args['id'];
        unset($args['id']);

        return (new \Tacit\Test\Controller\MockRestfulItem($this->settings, $this->router, $this->fractal, $this->repository))->handle($request, $response, $args);
    })->setName('MockRestfulItem');
});

$this->tacit->group('/monga', function () {
    $this->group('/collection', function () {
//        $this->any('[/]', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
//            return (new \Tacit\Test\Controller\Monga\RestfulCollection($this->settings, $this->router, $this->fractal, $this->repository))->handle($request, $response, $args);
//        })->setName('MongaRestfulCollection');
        $this->any('/{id}[/]', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
            $args['_id'] = $args['id'];
            unset($args['id']);

            return (new \Tacit\Test\Controller\Monga\RestfulItem($this->settings, $this->router, $this->fractal, $this->repository))->handle($request, $response, $args);
        })->setName('MongaRestfulItem');
    });
});

$this->tacit->group('/rethinkdb', function () {
    $this->group('/collection', function () {
//        $this->any('[/]', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
//            return (new \Tacit\Test\Controller\RethinkDB\RestfulCollection($this->settings, $this->router, $this->fractal, $this->repository))->handle($request, $response, $args);
//        })->setName('RethinkDBRestfulCollection');
        $this->any('/{id}[/]', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
            return (new \Tacit\Test\Controller\RethinkDB\RestfulItem($this->settings, $this->router, $this->fractal, $this->repository))->handle($request, $response, $args);
        })->setName('RethinkDBRestfulItem');
    });
});

$this->tacit->any('/basic-test[/]', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
    $mockRestful = new \Tacit\Test\Controller\MockRestful($this->settings, $this->router, $this->fractal);
    if ((new \Tacit\Authorize\Basic($this->settings->get('tacit.identitiesFile', __DIR__ . '/identities.php')))->isValidRequest($request)) {
        return $mockRestful->handle($request, $response, $args);
    } else {
        $response->getBody()->write('Unauthorized Error');
        return $response->withStatus(401);
    }
});

$this->tacit->any('/hmac-test[/]', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
    $mockRestful = new \Tacit\Test\Controller\MockRestful($this->settings, $this->router, $this->fractal);
    if ((new \Tacit\Authorize\HMAC($this->settings->get('tacit.identitiesFile', __DIR__ . '/identities.php')))->isValidRequest($request)) {
        return $mockRestful->handle($request, $response, $args);
    } else {
        $response->getBody()->write('Unauthorized Error');
        return $response->withStatus(401);
    }
});
