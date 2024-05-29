<?php

declare(strict_types=1);

namespace PHPOMG\Psr15;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequestHandler implements RequestHandlerInterface
{
    private $handler;
    private $middlewares = [];

    public function setHandler(
        RequestHandlerInterface $handler
    ) {
        $this->handler = $handler;
    }

    public function pushMiddleware(MiddlewareInterface ...$middlewares)
    {
        array_push($this->middlewares, ...$middlewares);
    }

    public function unShiftMiddleware(MiddlewareInterface ...$middlewares)
    {
        array_unshift($this->middlewares, ...$middlewares);
    }

    public function popMiddleware(): ?MiddlewareInterface
    {
        return array_pop($this->middlewares);
    }

    public function shiftMiddleware(): ?MiddlewareInterface
    {
        return array_shift($this->middlewares);
    }

    public function handle(ServerRequestInterface $serverRequest): ResponseInterface
    {
        if ($middleware = self::shiftMiddleware()) {
            return $middleware->process($serverRequest, $this);
        } else {
            return $this->handler->handle($serverRequest);
        }
    }
}
