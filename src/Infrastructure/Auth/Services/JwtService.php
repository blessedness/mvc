<?php

declare(strict_types=1);


namespace Infrastructure\Auth\Services;


class JwtService
{
    private const KEY = '5b0a951f382157ea382783a2aa445da9';
    private const ALG_SHA256 = 'SHA256';

    public function encode(array $data)
    {
        $segments = [];
        $head = $this->generateHeader();

        $segments[] = $this->base64encode(
            $this->jsonEncode($head)
        );

        $segments[] = $this->base64encode(
            $this->jsonEncode($data)
        );

        $signingInput = implode('.', $segments);

        $segments[] = $this->base64encode(
            $this->generateSignature($head['alg'], $signingInput, self::KEY)
        );

        return implode('.', $segments);
    }

    protected function generateHeader()
    {
        return [
            'alg' => self::ALG_SHA256,
            'typ' => 'JWT'
        ];
    }

    protected function base64encode($data)
    {
        return str_replace('=', '', strtr(base64_encode($data), '+/', '-_'));
    }

    protected function jsonEncode($data)
    {
        return json_encode($data);
    }

    protected function generateSignature(string $algorithm, string $msg, string $key)
    {
        return hash_hmac($algorithm, $msg, $key, false);
    }

    public function decode(string $jwt)
    {
        $arguments = explode('.', $jwt);
        if (count($arguments) !== 3) {
            throw new \DomainException('Wrong number of segments');
        }

        [$head, $body, $crypto] = $arguments;

        if (null === ($header = $this->jsonDecode($this->base64decode($head)))) {
            throw new \DomainException('Invalid header encoding');
        }

        if (null === ($payload = $this->jsonDecode($this->base64decode($body)))) {
            throw new \DomainException('Invalid claims encoding');
        }

        if (false === ($sig = $this->base64decode($crypto))) {
            throw new \DomainException('Invalid signature encoding');
        }

        if (empty($header->alg)) {
            throw new \DomainException('Empty algorithm');
        }

        if ($header->alg !== self::ALG_SHA256) {
            throw new \DomainException('Algorithm not allowed');
        }

        // Check the signature
        if (!$this->verify($sig, self::ALG_SHA256, "$head.$body", self::KEY)) {
            throw new \DomainException('Signature verification failed');
        }

        $timestamp = time();

        // Check that this token has been created before 'now'. This prevents
        // using tokens that have been created for later use (and haven't
        // correctly used the nbf claim).
        if (!isset($payload->iat) || $payload->iat > ($timestamp)) {
            throw new \DomainException(
                'Cannot handle token prior to ' . date(\DateTime::ISO8601, $payload->iat)
            );
        }

        // Check if this token has expired.
        if (isset($payload->exp) && ($timestamp) >= $payload->exp) {
            throw new \DomainException('Expired token');
        }

        return $payload;
    }

    protected function jsonDecode(string $data)
    {
        return json_decode($data);
    }

    protected function base64decode(string $data)
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $padlen = 4 - $remainder;
            $data .= str_repeat('=', $padlen);
        }
        return base64_decode(strtr($data, '-_', '+/'));
    }

    protected function verify(string $signature, string $algorithm, string $msg, string $key)
    {
        $hash = hash_hmac($algorithm, $msg, $key, false);

        return hash_equals($signature, $hash);
    }
}