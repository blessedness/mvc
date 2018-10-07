<?php

declare(strict_types=1);


namespace Core\Router;


use Core\Http\{RequestInterface, Response};
use Core\Router\Exception\{MethodNotAllowed, NotFoundException, RouteNotFoundException};


class Router
{
    /**
     * @var RouteCollection
     */
    private $routes;

    public function __construct(RouteCollection $routes)
    {
        $this->routes = $routes;
    }

    public function match(RequestInterface $request): Result
    {
        foreach ($this->routes->getRoutes() as $route) {
            // replace route {id} to regex
            $pattern = preg_replace_callback('~\{([^\}]+)\}~', function ($matches) use ($route) {
                $argument = $matches[1]; // ID
                $replace = $route->getTokens()[$argument] ?? '[^}]+';
                return '(?P<' . $argument . '>' . $replace . ')'; //
            }, $route->getPattern());

            if (preg_match('~^' . $pattern . '$~i', $request->getPath(), $matches)) {

                if ($route->getMethods() && !in_array($request->getMethod(), $route->getMethods(), true)) {
                    throw new MethodNotAllowed(
                        Response::HTTP_METHOD_NOT_ALLOWED,
                        sprintf(
                            'No route found for "%s %s": Method Not Allowed (Allow: %s)',
                            $request->getMethod(),
                            $request->getPath(),
                            implode(', ', $route->getMethods())
                        )
                    );
                }

                return new Result(
                    $route->getName(),
                    $route->getHandler(),
                    array_filter($matches, '\is_string', ARRAY_FILTER_USE_KEY) // filter non string array keys
                );
            }
        }

        throw new NotFoundException(404, 'Page not found.');
    }

    public function generate(string $name, array $params = []): string
    {
        $arguments = array_filter($params);

        foreach ($this->routes->getRoutes() as $route) {
            if ($name !== $route->getName()) {
                continue;
            }

            $url = preg_replace_callback('~\{([^\}]+)\}~', function ($matches) use (&$arguments) {
                $argument = $matches[1];
                if (!array_key_exists($argument, $arguments)) {
                    throw new \InvalidArgumentException(sprintf('Missing parameter "%s"', $argument));
                }

                return $arguments[$argument];
            }, $route->getPattern());

            if (!is_null($url)) {
                return $url;
            }
        }

        throw new RouteNotFoundException($name, $params);
    }
}