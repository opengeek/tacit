<?php
/*
 * This file is part of the Tacit package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */ 

$tacit->any('/', function () use ($tacit) {
    (new \Tacit\Test\Controller\MockRestful($tacit))->handle();
});

$tacit->any('/hmac-test', function () use ($tacit) {
    $mockRestful = new \Tacit\Test\Controller\MockRestful($tacit);
    if ((new \Tacit\Authorize\HMAC())->isValidRequest($mockRestful)) {
        $mockRestful->handle();
    } else {
        $tacit->halt(401, 'Unauthorized Error');
    }
});
