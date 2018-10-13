<?php
declare(strict_types=1);

use App\Controllers\Auth\AuthLoginController;
use App\Controllers\User\UserIndexController;
use App\Middleware\JwtAuthMiddleware;
use Core\Router\RouteCollection;


$container->set('router', function () use ($container) {
    $routes = new RouteCollection();
    $routes->get('home', '/', UserIndexController::class);
    $routes->add('view', '/view/{id}', 'App\Controllers\User\UserViewController@view', ['GET'], ['id' => '\S+']);
    $routes->post('login', '/login', AuthLoginController::class);
    $routes->get('secured-home', '/secured/home', [
        JwtAuthMiddleware::class,
        UserIndexController::class
    ]);

    return $routes;
});
