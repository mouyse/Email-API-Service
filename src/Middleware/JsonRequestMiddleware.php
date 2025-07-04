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
            http_response_code(415);
            echo json_encode(['message' => 'Content-Type must be application/json']);
            exit();
        }
        if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') === false) {
            http_response_code(406);
            echo json_encode(['message' => 'Accept header must allow application/json']);
            exit();
        }
    }
}