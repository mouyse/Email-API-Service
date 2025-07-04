<?php
use PHPUnit\Framework\TestCase;
use Src\Middleware\JsonRequestMiddleware;

class JsonRequestMiddlewareTest extends TestCase
{
    public function testHandleExitsOnInvalidContentType()
    {
        $_SERVER['CONTENT_TYPE'] = 'text/plain';
        $_SERVER['HTTP_ACCEPT'] = 'application/json';

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Content-Type must be application/json');
        $this->expectExceptionCode(415);
        JsonRequestMiddleware::handle();
    }

    public function testHandleExitsOnInvalidAcceptHeader()
    {
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $_SERVER['HTTP_ACCEPT'] = 'text/html';

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Accept header must allow application/json');
        $this->expectExceptionCode(406);
        JsonRequestMiddleware::handle();
    }

    public function testHandlePassesOnValidHeaders()
    {
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $_SERVER['HTTP_ACCEPT'] = 'application/json';

        JsonRequestMiddleware::handle();
        $this->assertTrue(true);
    }
}