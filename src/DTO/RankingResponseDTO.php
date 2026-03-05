<?php

declare(strict_types=1);

namespace App\DTO;

class RankingResponseDTO
{
    // Representa a resposta completa do Service. O array deve conter instâncias de RankingItemDTO
    public function __construct(
        public readonly string $movement,
        /** @var RankingItemDTO[] */
        public readonly array $ranking
    ) {}
}