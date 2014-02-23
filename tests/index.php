<?php
/*
 * This file is part of the Tacit package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */ 

require 'bootstrap.php';

/** @var \Tacit\Tacit $tacit */
$tacit = new \Tacit\Tacit(
    [
        'mode' => 'development',
        'startTime' => microtime(true),
        'connection' => [
            'class' => 'Tacit\\Test\\Model\\MockRepository',
            'server' => 'localhost',
            'options' => array('connect' => false),
            'repository' => 'tacit_test'
        ],
        'tacit.identitiesFile' => __DIR__ . '/identities.php'
    ]
);

require 'routes.php';

$tacit->run();
