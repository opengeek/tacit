<?php
/*
 * This file is part of the Tacit package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tacit\Handlers;


use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Stream;
use Tacit\Configurable;
use Tacit\Controller\Exception\RestfulException;

class Error
{
    use Configurable;

    protected $defaultStatus = 500;

    public function __construct(array $configuration)
    {
        $this->configuration = $configuration;
    }

    function __invoke(ServerRequestInterface $request, ResponseInterface $response, Exception $e)
    {
        $resource = [
            'status' => $this->defaultStatus,
            'code' => $e->getCode(),
            'message' => $e->getMessage()
        ];
        if ($e instanceof RestfulException) {
            $resource['status'] = $e->getStatus();
            $resource['description'] = $e->getDescription();
            $resource['property'] = $e->getProperty();
        }
        if ($this->getOption('debug') === true) {
            $resource['request_duration'] = microtime(true) - $this->getOption('startTime');
        }
        $response->withBody(new Stream(fopen('php://temp', 'r+')));

        return $response->withJson($resource, $resource['status'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}