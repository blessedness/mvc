<?php

declare(strict_types=1);


namespace Core\Http\Pipeline;


use Core\Http\RequestInterface;

class MiddlewareResolver
{
    public function resolve($handler): callable
    {
        if (is_array($handler)) {
            $pipeline = new Pipeline();
            foreach ($handler as $item) {
                $pipeline->pipe($this->resolve($item));
            }

            return $pipeline;
        }

        if ($handler instanceof \Closure) {
            return function (RequestInterface $request, callable $next) use ($handler) {
                return $handler($request, $next);
            };
        }

        if (is_string($handler)) {
            return function (RequestInterface $request, callable $next) use ($handler) {

                $middleware = $this->resolve(new $handler());

                return $middleware($request, $next);
            };
        }

        if (is_object($handler)) {
            $reflection = new \ReflectionObject($handler);
            if ($reflection->hasMethod('__invoke')) {
                $method = $reflection->getMethod('__invoke');
                $parameters = $method->getParameters();
                if (count($parameters) === 2 && $parameters[1]->isCallable()) {
                    return function (RequestInterface $request, callable $next) use ($handler) {
                        return $handler($request, $next);
                    };
                }
            }

            return $handler;
        }

        throw new \LogicException('Middleware not supported.');
    }
}