<?php

declare(strict_types=1);


namespace Core\Http;


class RequestFactory
{
    public static function init(array $query = null, array $body = null): Request
    {
        return (new Request($query ?: $_GET, $body ?: $_POST));
    }
}