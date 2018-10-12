<?php

declare(strict_types=1);

use App\Controllers\Auth\AuthLoginController;
use App\Controllers\User\UserIndexController;
use App\Middleware\JwtAuthMiddleware;
use Core\Application;
use Core\Router\{RouteCollection};

require __DIR__ . '/src/autoload.php';

$container = new \Core\Container\Container();

$container->set(JwtAuthMiddleware::class, function () {
    return new JwtAuthMiddleware();
});

$container->set('router', function () use ($container) {
    $routes = new RouteCollection();
    $routes->add('home', '/', UserIndexController::class, ['GET']);
    $routes->add('view', '/view/{id}', 'App\Controllers\User\UserViewController@view', ['GET'], ['id' => '\S+']);
    $routes->add('login', '/login', AuthLoginController::class, ['POST']);
    $routes->add('secured-home', '/secured/home', [
        $container->get(JwtAuthMiddleware::class),
        UserIndexController::class
    ], ['GET']);

    return $routes;
});

(new Application($container))
    ->pipe(\App\Middleware\ProfilerMiddleware::class)
    ->run();

function dump($data)
{
    echo '<pre>';
    var_dump($data);
    die();
}

function prnx($data)
{
    dump($data);
}