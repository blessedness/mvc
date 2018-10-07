<?php

declare(strict_types=1);


namespace Core\Router\Exception;


use Core\Exception\HttpException;

class NotFoundException extends HttpException
{
    public function __construct(int $status = 404, ?string $message = null, int $code = 0, \Exception $previous = null)
    {
        parent::__construct($status, $message, $code, $previous);
    }
}