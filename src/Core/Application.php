<?php

declare(strict_types=1);


namespace Core;

use App\Middleware\NotFoundHandlerMiddleware;
use Core\Exception\HttpExceptionInterface;
use Core\Http\{JsonResponse,
    Middleware\RouteDispatchMiddleware,
    Middleware\RouteMiddleware,
    Pipeline\MiddlewareResolver,
    Pipeline\Pipeline,
    RequestFactory,
    RequestInterface,
    ResponseInterface};
use Core\Router\{RouteCollection, Router};

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

            $router = new Router($this->routes);
            $this->pipe(new RouteMiddleware($router, $this->resolver));
            $this->pipe(new RouteDispatchMiddleware($this->resolver));

            $response = ($this->pipeline)($request, new NotFoundHandlerMiddleware());

            if (!$response instanceof ResponseInterface) {
                throw new \RuntimeException(sprintf('Response class must implements "%s" interface', ResponseInterface::class));
            }

            $response->send();
        } catch (\Exception $e) {
            $this->resolveExceptionException($request, $e);
        }
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

    /**
     * @param RequestInterface $request
     * @param \Exception|HttpExceptionInterface $exception
     */
    public function resolveExceptionException(RequestInterface $request, \Exception $exception)
    {
        if (method_exists($exception, 'getStatusCode')) {
            $statusCode = $exception->getStatusCode();
        } else {
            $statusCode = 500;
        }

        if (method_exists($exception, 'getMessages')) {
            $error['message'] = $exception->getMessages();
        } else {
            $error['message'] = $exception->getMessage();
        }

        $response = new JsonResponse(compact('error'), $statusCode);

        if (method_exists($exception, 'getHeaders')) {
            foreach ($exception->getHeaders() as $header => $value) {
                $response = $response->withHeader($header, $value);
            }
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