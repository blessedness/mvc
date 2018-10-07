<?php

declare(strict_types=1);


namespace App\Controllers;

use Core\Http\JsonResponse;

abstract class AbstractController
{
    public function render($content, ?int $status = null)
    {
        return new JsonResponse($content, $status);
    }
}