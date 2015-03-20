<?php
/*
 * This file is part of the Tacit package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$this->tacit->any('/', function () {
    (new \Tacit\Test\Controller\MockRestful($this->tacit))->handle();
});

$this->tacit->group('/collection', function () {
    $this->tacit->any('/', function () {
        (new \Tacit\Test\Controller\MockRestfulCollection($this->tacit))->handle();
    })->name('MockRestfulCollection');
    $this->tacit->any('/:_id', function ($_id) {
        (new \Tacit\Test\Controller\MockRestfulItem($this->tacit))->handle($_id);
    })->name('MockRestfulItem');
});

$this->tacit->any('/basic-test', function () {
    $mockRestful = new \Tacit\Test\Controller\MockRestful($this->tacit);
    if ((new \Tacit\Authorize\Basic())->isValidRequest($mockRestful)) {
        $mockRestful->handle();
    } else {
        $this->tacit->halt(401, 'Unauthorized Error');
    }
});

$this->tacit->any('/hmac-test', function () {
    $mockRestful = new \Tacit\Test\Controller\MockRestful($this->tacit);
    if ((new \Tacit\Authorize\HMAC())->isValidRequest($mockRestful)) {
        $mockRestful->handle();
    } else {
        $this->tacit->halt(401, 'Unauthorized Error');
    }
});
