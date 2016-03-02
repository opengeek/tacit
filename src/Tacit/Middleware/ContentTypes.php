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

    /**
     * Parse input
     *
     * This method will attempt to parse the request body
     * based on its content type if available.
     *
     * @param  string $input
     * @param  string $contentType
     * @return mixed
     */
    protected function parse ($input, $contentType)
    {
        if (isset($this->contentTypes[$contentType]) && is_callable($this->contentTypes[$contentType])) {
            $result = call_user_func($this->contentTypes[$contentType], $input);
            if ($result !== false) {
                return $result;
            }
        }

        return $input;
    }

    /**
     * Parse JSON
     *
     * This method converts the raw JSON input
     * into an associative array.
     *
     * @param  string       $input
     * @return array|string
     */
    protected function parseJson($input)
    {
        if (function_exists('json_decode')) {
            $result = json_decode($input, true);
            if(json_last_error() === JSON_ERROR_NONE) {
                return !empty($result) ? $result : [];
            }
        }
    }
}
