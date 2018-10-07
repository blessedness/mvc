<?php

declare(strict_types=1);


namespace Core\Http;


interface ResponseInterface
{
    public function getHeader($header);

    public function hasHeader($header): bool;

    public function send();

    public function withHeader($header, $value);

    /**
     * @return string
     */
    public function getProtocolVersion(): string;

    public function getStatusCode(): int;

    public function getStatusText();

    public function getHeaders(): array;

    public function getBody();

    public function setHeader($header, $value): void;
}