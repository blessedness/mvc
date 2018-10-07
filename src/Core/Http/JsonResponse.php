<?php

declare(strict_types=1);


namespace Core\Http;

class JsonResponse extends Response
{
    public function __construct($body = '', ?int $status = self::HTTP_OK, bool $isJson = false)
    {
        parent::__construct($body, $status);

        if (!$isJson) {
            $this->setBody(json_encode($body));
        }

        $this->setHeader('Content-Type', 'application/json');
    }
}