<?php

declare(strict_types=1);


namespace Infrastructure\Auth\Exception;

use Core\Exception\HttpException;
use Core\Http\Response;

class UnauthorizedHttpException extends HttpException
{
    public function __construct(int $status = null, string $message = null, ?int $code = 0, ?\Exception $previous = null, ?array $headers = [])
    {
        parent::__construct(Response::HTTP_UNAUTHORIZED, 'Unauthenticated.', $code, $previous, $headers);
    }
}