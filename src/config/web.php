<?php

declare(strict_types=1);

require 'debug.php';

use App\Controllers\Auth\AuthLoginController;
use App\Controllers\User\UserIndexController;
use App\Middleware\JwtAuthMiddleware;
use Core\Container\ContainerInterface;
use Core\Http\Pipeline\MiddlewareResolver;
use Core\Router\RouteCollection;


return [
    'router' => function () {
        $routes = new RouteCollection();
        $routes->get('home', '/', UserIndexController::class);
        $routes->add('view', '/view/{id}', 'App\Controllers\User\UserViewController@view', ['GET'], ['id' => '\S+']);
        $routes->post('login', '/login', AuthLoginController::class);
        $routes->get('secured-home', '/secured/home', [
            JwtAuthMiddleware::class,
            UserIndexController::class
        ]);

        return $routes;
    },
    MiddlewareResolver::class => function (ContainerInterface $container) {
        return new MiddlewareResolver($container);
    }
];