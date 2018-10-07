<?php

declare(strict_types=1);


namespace Core\Http\Pipeline;


use Core\Http\RequestInterface;
use Core\Http\ResponseInterface;

class Next
{
    /**
     * @var \SplQueue
     */
    private $queue;
    /**
     * @var callable
     */
    private $default;

    public function __construct(\SplQueue $queue, callable $default)
    {
        $this->queue = $queue;
        $this->default = $default;
    }

    public function __invoke(RequestInterface $request): ResponseInterface
    {
        if ($this->queue->isEmpty()) {
            return ($this->default)($request);
        }

        $current = $this->queue->dequeue();

        return $current($request, function (RequestInterface $request) {
            return $this($request);
        });
    }
}