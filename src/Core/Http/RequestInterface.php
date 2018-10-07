<?php

declare(strict_types=1);


namespace Core\Http;


interface RequestInterface
{
    /**
     * Current request method
     *
     * @return string
     */
    public function getMethod(): string;

    /**
     * Requested path
     *
     * @return string
     */
    public function getPath(): string;

    /**
     * @param $attribute
     * @param $value
     * @return self
     */
    public function withAttribute($attribute, $value);

    /**
     * Is current request is json
     * @return bool
     */
    public function isJson(): bool;

    /**
     * Get all requested params
     * @return array
     */
    public function getAttributes(): array;

    /**
     * Find value in requested params by attribute name
     * @param string $name
     * @return mixed
     */
    public function getAttribute(string $name);

    /**
     * Body data from request
     * @return mixed
     */
    public function getContent();

    public function getHeader(string $name, $default = null);

    public function getHeaders();
}