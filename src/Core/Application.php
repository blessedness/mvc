<?php

declare(strict_types=1);


namespace Core;

use App\Middleware\NotFoundHandlerMiddleware;
use Core\Container\Container;
use Core\Container\ContainerInterface;
use Core\Exception\HttpExceptionInterface;
use Core\Http\{JsonResponse,
    Middleware\RouteDispatchMiddleware,
    Middleware\RouteMiddleware,
    Pipeline\MiddlewareResolver,
    Pipeline\Pipeline,
    RequestFactory,
    RequestInterface,
    ResponseInterface};
use Core\Router\{Router};

class Application
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(array $config)
    {
        $this->container = new Container($config);
        $this->init();
    }

    public function init()
    {
        $this->container->set(RouteMiddleware::class, function () {
            $router = new Router($this->container->get('router'));
            return new RouteMiddleware($router, $this->getResolver());
        });

        $this->container->set(RouteDispatchMiddleware::class, function () {
            return new RouteDispatchMiddleware($this->getResolver());
        });

        $this->pipe(RouteMiddleware::class);
        $this->pipe(RouteDispatchMiddleware::class);
    }

    public function getResolver(): MiddlewareResolver
    {
        return $this->container->get(MiddlewareResolver::class);
    }

    /**
     * Add middleware to pipeline
     *
     * @param $middleware
     * @return Application
     */
    public function pipe($middleware): Application
    {
        $this->getPipeline()->pipe(
            $this->getResolver()->resolve($middleware)
        );

        return $this;
    }

    public function getPipeline(): Pipeline
    {
        return $this->container->get(Pipeline::class);
    }

    /**
     * Application run
     */
    public function run()
    {
        try {
            $request = RequestFactory::init();

            $response = ($this->getPipeline())($request, $this->container->get(NotFoundHandlerMiddleware::class));

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
}