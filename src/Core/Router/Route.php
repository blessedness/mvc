<?php

declare(strict_types=1);


namespace Core\Router;


class Route
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $pattern;
    /**
     * @var
     */
    private $handler;
    /**
     * @var array
     */
    private $methods;
    /**
     * @var array
     */
    private $tokens;

    public function __construct(string $name, string $pattern, $handler, array $methods, array $tokens)
    {
        $this->name = $name;
        $this->pattern = $pattern;
        $this->handler = $handler;
        $this->methods = $methods;
        $this->tokens = $tokens;
    }

    /**
     * @return string
     */
    public function getPattern(): string
    {
        return $this->pattern;
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
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * @return array
     */
    public function getTokens(): array
    {
        return $this->tokens;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}