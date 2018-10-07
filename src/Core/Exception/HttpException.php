<?php

declare(strict_types=1);

namespace Core\Exception;

class HttpException extends \Exception implements HttpExceptionInterface
{
    /**
     * @var int HTTP status code, such as 403, 404, 500, etc.
     */
    protected $statusCode;

    protected $headers;

    /**
     * Constructor.
     * @param int $status HTTP status code, such as 404, 500, etc.
     * @param string $message error message
     * @param int $code error code
     * @param \Exception $previous The previous exception used for the exception chaining.
     * @param array $headers
     */
    public function __construct(int $status = null, string $message = null, $code = 0, \Exception $previous = null, array $headers = [])
    {
        $this->statusCode = $status;
        $this->headers = $headers;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return int
     */
    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }

    /**
     * @return mixed
     */
    public function getHeaders()
    {
        return $this->headers;
    }
}