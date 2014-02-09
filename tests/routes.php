<?php
/*
 * This file is part of the Tacit package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */ 

$tacit->get('/', function () use ($tacit) {
    (new \Tacit\Test\Controller\MockRestful($tacit))->get();
});
