<?php

declare(strict_types=1);


namespace Core\Router;


class RouteCollection
{
    private $routes = [];

    public function any(string $name, string $pattern, $handler, array $tokens = []): void
    {
        $this->add($name, $pattern, $handler, [], $tokens);
    }

    /**
     * @param string $name
     * @param string $pattern
     * @param string|array|\Closure|object $handler
     * @param array|string $methods
     * @param array $tokens
     */
    public function add(string $name, string $pattern, $handler, $methods, array $tokens = []): void
    {
        $this->routes[] = new Route($name, $pattern, $handler, (array)$methods, $tokens);
    }

    public function get(string $name, string $pattern, $handler, array $tokens = []): void
    {
        $this->add($name, $pattern, $handler, ['GET'], $tokens);
    }

    public function post(string $name, string $pattern, $handler, array $tokens = []): void
    {
        $this->add($name, $pattern, $handler, ['POST'], $tokens);
    }

    public function put(string $name, string $pattern, $handler, array $tokens = []): void
    {
        $this->add($name, $pattern, $handler, ['PUT'], $tokens);
    }

    public function patch(string $name, string $pattern, $handler, array $tokens = []): void
    {
        $this->add($name, $pattern, $handler, ['PATCH'], $tokens);
    }

    public function delete(string $name, string $pattern, $handler, array $tokens = []): void
    {
        $this->add($name, $pattern, $handler, ['DELETE'], $tokens);
    }

    /**
     * @return Route[]
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }
}