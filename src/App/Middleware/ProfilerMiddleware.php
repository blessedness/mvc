<?php

declare(strict_types=1);


namespace App\Middleware;


use Core\Http\MiddlewareInterface;
use Core\Http\RequestInterface;
use Core\Http\ResponseInterface;

class ProfilerMiddleware implements MiddlewareInterface
{
    public function __invoke(RequestInterface $request, callable $next): ResponseInterface
    {
        $start = microtime(true);

        /* @var $response ResponseInterface */
        $response = $next($request);

        $stop = microtime(true);

        return $response->withHeader('X-Profiler-Time', $stop - $start);
    }
}