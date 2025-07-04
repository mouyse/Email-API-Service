<?php

namespace Src\Middleware;

/**
 * Class JsonRequestMiddleware
 * Middleware to validate JSON request headers (Content-Type and Accept).
 */
class JsonRequestMiddleware
{
    /**
     * Validates the request headers for JSON API compliance.
     *
     * @return void
     */
    public static function handle()
    {
        if ($_SERVER['CONTENT_TYPE'] !== 'application/json') {
            throw new \RuntimeException('Content-Type must be application/json', 415);
        }
        if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') === false) {
            throw new \RuntimeException('Accept header must allow application/json', 406);
        }
    }
}