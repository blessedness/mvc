<?php

declare(strict_types=1);


namespace Core\Http\Middleware;


use Core\Http\MiddlewareInterface;
use Core\Http\Pipeline\MiddlewareResolver;
use Core\Http\RequestInterface;
use Core\Http\ResponseInterface;
use Core\Router\Result;
use Core\Router\Route;

class RouteDispatchMiddleware implements MiddlewareInterface
{
    /**
     * @var MiddlewareResolver
     */
    private $resolver;

    public function __construct(MiddlewareResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    public function __invoke(RequestInterface $request, callable $next): ResponseInterface
    {
        /* @var $result Route */
        if (!$result = $request->getAttribute(Result::class)) {
            return $next($request);
        }

        $handler = $this->resolveHandler($result->getHandler(), $request);

        $middleware = $this->resolver->resolve($handler);

        return $middleware($request, $next);
    }

    protected function resolveHandler($handler, RequestInterface $request)
    {
        if (is_array($handler)) {
            return $handler;
        }

        $exploded = explode('@', $handler);

        if (count($exploded) === 1) {
            return $handler;
        } else {
            return function () use ($exploded, $request) {
                return call_user_func([new $exploded[0], $exploded[1]], $request);
            };
        }
    }
}