<?php

declare(strict_types=1);

namespace App\Core;

class Router
{
    public function resolve(string $method, string $uri): array
    {
        $path = rtrim(parse_url($uri, PHP_URL_PATH), '/');
        parse_str($_SERVER['QUERY_STRING'] ?? '', $query);

        if ($method === 'GET' && $path === '/api/v1/movements/ranking') {
            $movementId = isset($query['id']) && ctype_digit($query['id']) ? (int)$query['id'] : null;
            $movementName = $query['name'] ?? null;

            return [
                'controller' => 'ranking',
                'movement_id' => $movementId,
                'movement_name' => $movementName
            ];
        }

        return [
            'controller' => null,
        ];
    }
}