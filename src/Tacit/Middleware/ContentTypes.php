<?php
/*
 * This file is part of the Tacit package.
 *
 * Copyright (c) Jason Coward <jason@opengeek.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tacit\Middleware;


class ContentTypes extends \Slim\Middleware\ContentTypes
{
    public function call()
    {
        $env = $this->app->environment();
        $env['slim.input_original'] = $env['slim.input'];

        $mediaType = $this->app->request()->getMediaType();
        if ($mediaType && isset($this->contentTypes[$mediaType])) {
            $env['slim.request.form_hash'] = $env['slim.input'] = $this->parse($env['slim.input'], $mediaType);
        }
        $this->next->call();
    }
}
