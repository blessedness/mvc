<?php

declare(strict_types=1);


namespace Core\Http;


interface MiddlewareInterface
{
    public function __invoke(RequestInterface $request, callable $next): ResponseInterface;
}