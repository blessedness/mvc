<?php

declare(strict_types=1);


namespace Core\Container;


interface ContainerInterface
{
    public function has(string $id): bool;

    public function get(string $id);

    public function set(string $id, $value);
}