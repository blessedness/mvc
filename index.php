<?php

declare(strict_types=1);

use Core\Application;
use Core\Container\Container;

require './src/autoload.php';

$container = new Container();

require './src/config/debug.php';
require './src/config/routes.php';
require './src/config/services.php';

(new Application($container))
    ->pipe(\App\Middleware\ProfilerMiddleware::class)
    ->run();