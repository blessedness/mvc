<?php

declare(strict_types=1);


namespace Core\Router;


class RouteCollection
{
    private $routes = [];

    /**
     * @param string $name
     * @param string $pattern
     * @param $handler
     * @param array $methods
     * @param array $tokens
     */
    public function add(string $name, string $pattern, $handler, array $methods = [], array $tokens = []): void
    {
        $this->routes[] = new Route($name, $pattern, $handler, (array)$methods, $tokens);
    }

    /**
     * @return Route[]
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }
}