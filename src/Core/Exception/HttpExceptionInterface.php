<?php

declare(strict_types=1);


namespace Core\Exception;


interface HttpExceptionInterface
{
    public function getStatusCode(): ?int;
}