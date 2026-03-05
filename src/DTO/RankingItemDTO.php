<?php

declare(strict_types=1);

namespace App\DTO;

class RankingItemDTO
{

    // Estrutura padrão para retornos válidos
    public function __construct(
        public readonly int $position,
        public readonly string $user,
        public readonly float $personalRecord,
        public readonly string $date
    ) {}
}