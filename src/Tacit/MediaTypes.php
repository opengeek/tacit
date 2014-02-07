<?php
/*
 * This file is part of the Tacit package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tacit;

use Slim\Middleware\ContentTypes;

/**
 * Extends the Slim ContentTypes middleware to parse input for json and xml MediaTypes.
 *
 * @package Tacit
 */
class MediaTypes extends ContentTypes
{
    public function call()
    {
        $mediaType = $this->app->request()->getMediaType();
        if (in_array($mediaType, array('application/json', 'application/xml'))) {
            $env = $this->app->environment();
            $env['slim.request.form_hash'] = $this->parse($env['slim.input'], $mediaType);
        }
        $this->next->call();
    }
}
