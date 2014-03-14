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
use Slim\Slim;
use Tacit\Controller\Exception\ResourceConflictException;
use Tacit\Controller\Exception\UnauthorizedException;
use Tacit\Controller\Restful;
use Tacit\Tacit;

class HMAC implements Authorization
{
    private static $identities;

    public static function identities(Slim $app)
    {
        if (!is_array(static::$identities)) {
            $identitiesFile = $app->config('tacit.identitiesFile');
            if (is_readable($identitiesFile)) {
                static::$identities = include $identitiesFile;
            }
        }
        return static::$identities;
    }

    /**
     * Get the input data from the request to be used for validation.
     *
     * @param \Tacit\Tacit $app
     *
     * @return string A string representation of the input data elements.
     */
    public function getInput(Tacit $app)
    {
        return implode("\n", [
            $app->request->getMethod(),
            md5($app->environment['slim.input_original']),
            $app->request->headers('Content-Type'),
            $app->request->getResourceUri()
        ]);
    }

    /**
     * Get the signature provided by the client for validation.
     *
     * @param Request $request
     *
     * @return string The signature.
     */
    public function getSignature(Request $request)
    {
        return $request->headers('Signature-HMAC');
    }

    /**
     * Determine if the client has authorization to make the request.
     *
     * @param Restful $controller
     *
     * @throws \Tacit\Controller\Exception\UnauthorizedException
     * @throws \Tacit\Controller\Exception\ResourceConflictException
     * @return bool Returns true if the client has authorization to make the request.
     */
    public function isValidRequest(Restful $controller)
    {
        $request = $controller->getApp()->request;

        $signature = $this->getSignature($request);
        if (empty($signature)) {
            throw new UnauthorizedException(
                $controller,
                'Unsigned Request',
                'No valid authorization signature was provided with the request.',
                ['Signature-HMAC' => $signature]
            );
        }
        $exploded = explode(':', $signature, 3);
        if (count($exploded) !== 3) {
            throw new UnauthorizedException(
                $controller,
                'Invalid Signature',
                'The request contains an invalid authorization signature.',
                ['Signature-HMAC' => $signature]
            );
        }

        list($timestamp, $clientKey, $rawHash) = $exploded;

        $requested = hexdec($timestamp);
        $expires = $requested + (60*15);

        if (time() >= $expires) {
            throw new ResourceConflictException(
                $controller,
                'Request Outdated',
                'The signature indicates this request has expired and is no longer valid.',
                ['SignatureHMAC' => $signature]
            );
        }

        $secret = $this->getSecretKey($controller->getApp(), $clientKey);

        $fingerprint = $this->getInput($controller->getApp());
        $test = hash_hmac('sha1', $fingerprint, $secret);

        if ($test !== $rawHash) {
            throw new UnauthorizedException(
                $controller,
                'Unauthorized Signature',
                'The request is not properly signed and has been rejected.',
                ['Signature-HMAC' => $signature]
            );
        }
        return true;
    }

    public function getSecretKey($app, $clientKey)
    {
        $identities = static::identities($app);
        if (isset($identities[$clientKey])) {
            return $identities[$clientKey]['secretKey'];
        }
        return false;
    }
}
