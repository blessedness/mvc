<?php

declare(strict_types=1);

use App\Controllers\Auth\AuthLoginController;
use App\Controllers\User\UserIndexController;
use Core\Application;
use Core\Router\{RouteCollection};

require __DIR__ . '/src/autoload.php';

$routes = new RouteCollection();
$routes->add('home', '/', UserIndexController::class, ['GET']);
$routes->add('view', '/view/{id}', 'App\Controllers\User\UserViewController@view', ['GET'], ['id' => '\S+']);
$routes->add('login', '/login', AuthLoginController::class, ['POST']);
$routes->add('secured-home', '/secured/home', [
    new \App\Middleware\JwtAuthMiddleware(),
    UserIndexController::class
], ['GET']);

(new Application())
    ->pipe(\App\Middleware\ProfilerMiddleware::class)
    ->setRoutes($routes)
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