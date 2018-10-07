<?php

declare(strict_types=1);


namespace Infrastructure\Common\Exception;

use Core\Exception\HttpException;
use Core\Http\Response;

class FormException extends HttpException
{
    /**
     * @var array
     */
    private $messages = [];

    public function __construct(array $messages, int $status, ?string $message = null, int $code = 0, \Exception $previous = null)
    {
        $this->messages = $messages;
        parent::__construct($status, $message, $code, $previous);
    }

    /**
     * @param array $messages
     * @return static
     */
    public static function withMessages(array $messages)
    {
        return new static($messages, Response::HTTP_UNPROCESSABLE_ENTITY, '');
    }

    /**
     * @return array
     */
    public function getMessages(): array
    {
        return $this->messages;
    }
}