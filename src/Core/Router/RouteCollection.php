<?php

declare(strict_types=1);


namespace Core\Router;


class RouteCollection
{
    private $routes = [];

    public function any(string $name, string $pattern, $handler, array $tokens = []): void
    {
        $this->routes[] = new Route($name, $pattern, $handler, [], $tokens);
    }

    public function get(string $name, string $pattern, $handler, array $tokens = []): void
    {
        $this->routes[] = new Route($name, $pattern, $handler, ['GET'], $tokens);
    }

    public function post(string $name, string $pattern, $handler, array $tokens = []): void
    {
        $this->routes[] = new Route($name, $pattern, $handler, ['POST'], $tokens);
    }

    /**
     * @return Route[]
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }
}