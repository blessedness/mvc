<?php

declare(strict_types=1);

class Loader
{
    public static function register()
    {
        $map = require __DIR__ . '/psr4.php';

        foreach ($map as $namespace => $path) {
            spl_autoload_register(function ($className) {
                $class = dirname(__DIR__) . '/' . str_replace('\\', '/', $className) . '.php';
                require_once($class);
            }, true, true);
        }
    }
}