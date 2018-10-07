<?php

declare(strict_types=1);


namespace Infrastructure\Auth\Services;


class PasswordService
{
    public static function validatePassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}