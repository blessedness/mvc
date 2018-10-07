<?php

declare(strict_types=1);


namespace Core\Http\Pipeline;


use Core\Http\RequestInterface;
use Core\Http\ResponseInterface;

class Pipeline
{
    /**
     * @var \SplQueue
     */
    private $queue;

    public function __construct()
    {
        $this->queue = new \SplQueue();
    }

    public function pipe(callable $middleware): void
    {
        $this->queue->enqueue($middleware);
    }

    public function __invoke(RequestInterface $request, callable $default): ResponseInterface
    {
        return (new Next(clone $this->queue, $default))($request);
    }
}