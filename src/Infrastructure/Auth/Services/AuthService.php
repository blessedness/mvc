<?php

declare(strict_types=1);


namespace Infrastructure\Auth\Services;


class AuthService
{
    public function login(array $user): array
    {
        $now = time();

        $expire = $now + 3600 * 24 * 31;
        $jwtData = [
            'jti' => $user['id'],
            'iat' => $now,
            'exp' => $expire
        ];

        $token = (new JwtService())->encode($jwtData);

        return [
            'token' => $token,
            'expire' => $expire,
        ];
    }
}