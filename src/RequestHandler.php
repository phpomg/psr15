<?php

declare(strict_types=1);

namespace PHPOMG\Psr15;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequestHandler implements RequestHandlerInterface
{
    private $container;
    private $handler;

    private $middlewares = [];

    public function __construct(
        ContainerInterface $container,
        RequestHandlerInterface $requestHandler
    ) {
        $this->container = $container;
        $this->handler = $requestHandler;
    }

    public function pushMiddleware(...$middlewares): self
    {
        array_push($this->middlewares, ...$middlewares);
        return $this;
    }

    public function unShiftMiddleware(...$middlewares): self
    {
        array_unshift($this->middlewares, ...$middlewares);
        return $this;
    }

    public function popMiddleware(): ?MiddlewareInterface
    {
        if ($middleware = array_pop($this->middlewares)) {
            return $this->container->get($middleware);
        }
        return null;
    }

    public function shiftMiddleware(): ?MiddlewareInterface
    {
        if ($middleware = array_shift($this->middlewares)) {
            return $this->container->get($middleware);
        }
        return null;
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
