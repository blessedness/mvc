<?php

declare(strict_types=1);


namespace App\Middleware;


use Core\Http\RequestInterface;
use Core\Http\Response;
use Core\Router\Exception\NotFoundException;

class NotFoundHandlerMiddleware
{
    public function __invoke(RequestInterface $request)
    {
        throw new NotFoundException(Response::HTTP_NOT_FOUND, 'Page not found.');
    }
}