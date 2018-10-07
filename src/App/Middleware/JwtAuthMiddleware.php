<?php
declare(strict_types=1);


namespace App\Middleware;


use Core\Exception\HttpException;
use Core\Http\MiddlewareInterface;
use Core\Http\RequestInterface;
use Core\Http\ResponseInterface;
use Infrastructure\Auth\Exception\InvalidJwtTokenHttpException;
use Infrastructure\Auth\Exception\UnauthorizedHttpException;
use Infrastructure\Auth\Services\JwtService;

class JwtAuthMiddleware implements MiddlewareInterface
{
    public function __invoke(RequestInterface $request, callable $next): ResponseInterface
    {
        $authorizationHeader = $request->getHeader('Authorization');

        if (!$authorizationHeader) {
            throw new UnauthorizedHttpException();
        }

        $headerParts = explode(' ', $authorizationHeader);

        if (!(2 === count($headerParts) && 0 === strcasecmp($headerParts[0], 'Bearer'))) {
            throw new InvalidJwtTokenHttpException();
        }

        try {
            (new JwtService())->decode($headerParts[1]);
        } catch (\Exception $exception) {
            throw new HttpException(null, $exception->getMessage());
        }

        return $next($request);
    }
}