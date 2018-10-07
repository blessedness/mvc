<?php

declare(strict_types=1);


namespace App\Middleware;


use Core\Http\RequestInterface;
use Core\Router\Exception\NotFoundException;

class NotFoundHandlerMiddleware
{
    public function __invoke(RequestInterface $request)
    {
        throw new NotFoundException();
    }
}