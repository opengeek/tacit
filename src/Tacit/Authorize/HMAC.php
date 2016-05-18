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
use Tacit\Controller\Exception\ResourceConflictException;
use Tacit\Controller\Exception\UnauthorizedException;

class HMAC implements Authorization
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
        return implode("\n", [
            $request->getMethod(),
            md5((string)$request->getBody()),
            $request->getHeaderLine('Content-Type'),
            $request->getUri()->getPath()
        ]);
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
        return $request->getHeaderLine('Signature-HMAC');
    }

    /**
     * Determine if the client has authorization to make the request.
     *
     * @param ServerRequestInterface $request
     *
     * @return bool Returns true if the client has authorization to make the request.
     * @throws ResourceConflictException If the request has expired and is no longer valid.
     * @throws UnauthorizedException If no credentials are provided and the resource requires them for access.
     */
    public function isValidRequest(ServerRequestInterface $request)
    {
        $signature = $this->getSignature($request);
        if (empty($signature)) {
            throw new UnauthorizedException(
                'Unsigned Request',
                'No valid authorization signature was provided with the request.',
                ['Signature-HMAC' => $signature]
            );
        }
        $exploded = explode(':', $signature, 3);
        if (count($exploded) !== 3) {
            throw new UnauthorizedException(
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
                'Request Outdated',
                'The signature indicates this request has expired and is no longer valid.',
                ['SignatureHMAC' => $signature]
            );
        }

        $secret = $this->getSecretKey($clientKey);

        $fingerprint = $this->getInput($request);
        $test = hash_hmac('sha1', $fingerprint, $secret);

        if ($test !== $rawHash) {
            throw new UnauthorizedException(
                'Unauthorized Signature',
                'The request is not properly signed and has been rejected.',
                ['Signature-HMAC' => $signature]
            );
        }
        return true;
    }
}
