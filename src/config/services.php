<?php

declare(strict_types=1);


use App\Middleware\JwtAuthMiddleware;
use Core\Http\Pipeline\MiddlewareResolver;
use Core\Http\Pipeline\Pipeline;

$container->set(JwtAuthMiddleware::class, function () {
    return new JwtAuthMiddleware();
});

$container->set(Pipeline::class, function () {
    return new Pipeline();
});
$container->set(MiddlewareResolver::class, function () {
    return new MiddlewareResolver();
});