<?php

declare(strict_types=1);


namespace Core\Http\Pipeline;


use Core\Container\ContainerInterface;
use Core\Http\RequestInterface;

class MiddlewareResolver
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

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

                $middleware = $this->resolve($this->container->get($handler));

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