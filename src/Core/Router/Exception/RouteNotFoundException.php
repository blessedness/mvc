<?php

declare(strict_types=1);


namespace Core\Router\Exception;


class RouteNotFoundException extends \LogicException
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var array
     */
    private $params;

    public function __construct(string $name, array $params)
    {
        parent::__construct(sprintf('Route "%s" not found', $name));
        $this->name = $name;
        $this->params = $params;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }
}