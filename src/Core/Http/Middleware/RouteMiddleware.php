<?php

declare(strict_types=1);


namespace Core\Http\Middleware;


use Core\Http\MiddlewareInterface;
use Core\Http\Pipeline\MiddlewareResolver;
use Core\Http\RequestInterface;
use Core\Http\ResponseInterface;
use Core\Router\Exception\RouteNotFoundException;
use Core\Router\Result;
use Core\Router\Router;

class RouteMiddleware implements MiddlewareInterface
{
    /**
     * @var Router
     */
    private $router;
    /**
     * @var MiddlewareResolver
     */
    private $resolver;

    public function __construct(Router $router, MiddlewareResolver $resolver)
    {
        $this->router = $router;
        $this->resolver = $resolver;
    }

    public function __invoke(RequestInterface $request, callable $next): ResponseInterface
    {
        try {
            $result = $this->router->match($request);

            foreach ($result->getAttributes() as $attribute => $value) {
                $request = $request->withAttribute($attribute, $value);
            }

            return $next($request->withAttribute(Result::class, $result));
        } catch (RouteNotFoundException $exception) {
            return $next($request);
        }
    }
}