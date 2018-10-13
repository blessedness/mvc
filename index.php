<?php

declare(strict_types=1);

use Core\Application;

require './src/autoload.php';

$config = require './src/config/web.php';

(new Application($config))
    ->pipe(\App\Middleware\ProfilerMiddleware::class)
    ->run();