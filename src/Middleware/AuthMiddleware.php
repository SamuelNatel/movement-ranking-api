<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Http\Response;

class AuthMiddleware
{
    public static function handle(): void
    {
        $headers = getallheaders();

        if (!isset($headers['Authorization'])) {
            self::unauthorized();
        }

        $authHeader = $headers['Authorization'];

        if (!str_starts_with($authHeader, 'Bearer ')) {
            self::unauthorized();
        }

        $token = str_replace('Bearer ', '', $authHeader);
        $decoded = base64_decode($token);

        if (!$decoded || !str_contains($decoded, ':')) {
            self::unauthorized();
        }

        [$user, $pass] = explode(':', $decoded, 2);


        $envUser = $_ENV['API_AUTH_USER'] ?? '';
        $envPass = $_ENV['API_AUTH_PASSWORD'] ?? '';

        if ($user !== $envUser || $pass !== $envPass) {
            self::unauthorized();
        }
    }

    private static function unauthorized(): void
    {
        Response::error('Não autorizado!', 401);
    }
}