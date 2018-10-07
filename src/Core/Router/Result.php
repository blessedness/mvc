<?php

declare(strict_types=1);


namespace Core\Router;


class Result
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var
     */
    private $handler;
    /**
     * @var array
     */
    private $attributes;

    public function __construct(string $name, $handler, array $attributes)
    {
        $this->name = $name;
        $this->handler = $handler;
        $this->attributes = $attributes;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }
}