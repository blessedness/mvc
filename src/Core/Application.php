<?php

declare(strict_types=1);


namespace Core;

use App\Middleware\NotFoundHandlerMiddleware;
use Core\Exception\HttpExceptionInterface;
use Core\Http\{JsonResponse, Pipeline\MiddlewareResolver, Pipeline\Pipeline, RequestFactory, RequestInterface, ResponseInterface};
use Core\Router\{Exception\MethodNotAllowed, Exception\NotFoundException, RouteCollection, Router};
use Infrastructure\Common\Exception\FormException;

class Application
{
    /**
     * @var RouteCollection
     */
    private $routes;
    /**
     * @var MiddlewareResolver
     */
    private $resolver;
    /**
     * @var Pipeline
     */
    private $pipeline;

    public function __construct()
    {
        $this->resolver = new MiddlewareResolver();
        $this->pipeline = new Pipeline();
    }

    /**
     * Application run
     */
    public function run()
    {
        try {
            $request = RequestFactory::init();
            $response = $this->handleRequest($request);

            if (!$response instanceof ResponseInterface) {
                throw new \RuntimeException(sprintf('Response class must implements "%s" interface', ResponseInterface::class));
            }

            $response->send();
        } catch (\Exception $e) {
            $this->resolveExceptionException($request, $e);
        }
    }

    /**
     * @param RequestInterface $request
     * @return mixed
     * @throws NotFoundException
     * @throws MethodNotAllowed
     */
    public function handleRequest(RequestInterface $request)
    {
        $router = new Router($this->routes);

        $result = $router->match($request);

        foreach ($result->getAttributes() as $attribute => $value) {
            $request = $request->withAttribute($attribute, $value);
        }

        $this->pipe($this->resolveHandler($result->getHandler(), $request));

        return ($this->pipeline)($request, new NotFoundHandlerMiddleware());
    }

    /**
     * Add middleware to pipeline
     *
     * @param $middleware
     * @return Application
     */
    public function pipe($middleware): Application
    {
        $this->pipeline->pipe($this->resolver->resolve($middleware));

        return $this;
    }

    protected function resolveHandler($handler)
    {
        if (is_array($handler)) {
            return $handler;
        }

        $exploded = explode('@', $handler);

        if (count($exploded) === 1) {
            return $handler;
        } else {
            return function (RequestInterface $request) use ($exploded) {
                return call_user_func([new $exploded[0], $exploded[1]], $request);
            };
        }
    }

    /**
     * @param RequestInterface $request
     * @param \Exception|HttpExceptionInterface $exception
     */
    public function resolveExceptionException(RequestInterface $request, \Exception $exception)
    {
        if ($exception instanceof HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();
        } else {
            $statusCode = 500;
        }

        if ($exception instanceof FormException) {
            $message = $exception->getMessages();
        } else {
            $message = $exception->getMessage();
        }

        $response = new JsonResponse([
            'error' => $message,
        ], $statusCode);

        foreach ($exception->getHeaders() as $header => $value) {
            $response = $response->withHeader($header, $value);
        }

        $response->send();
    }

    /**
     * @param RouteCollection $routes
     * @return Application
     */
    public function setRoutes(RouteCollection $routes): Application
    {
        $this->routes = $routes;

        return $this;
    }
}