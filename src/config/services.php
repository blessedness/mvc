<?php

declare(strict_types=1);


use Core\Http\Pipeline\MiddlewareResolver;

$container->set(MiddlewareResolver::class, function () use ($container) {
    return new MiddlewareResolver($container);
});