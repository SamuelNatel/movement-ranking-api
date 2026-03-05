<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Infrastructure\Repository\RankingRepository;
use App\DTO\RankingItemDTO;
use App\DTO\RankingResponseDTO;
use DomainException;

class RankingService
{
    public function __construct(
        private RankingRepository $repository
    ) {}

    public function getRanking(?int $movementId, ?string $movementName = null): RankingResponseDTO
    {
        if ($movementId !== null) {
            $ranking = $this->repository->findRankingByMovementId($movementId);
            $movementName = $ranking[0]['movement_name'] ?? $this->repository->findMovementNameById($movementId);
        } elseif ($movementName !== null) {
            $ranking = $this->repository->findRankingByMovementName($movementName);
            $movementName = $ranking[0]['movement_name'] ?? $this->repository->findMovementNameByName($movementName);
        } else {
            throw new DomainException('Parâmetro do movimento inválido', 400);
        }

        if ($movementName === 'Unknown Movement') {
            throw new DomainException('Movimento não encontrado', 404);
        }

        $rankingDTOs = [];

        if (!empty($ranking)) {
            $rankingDTOs = array_map(fn($item) => new RankingItemDTO(
                position: isset($item['position']) ? (int) $item['position'] : 0,
                user: $item['user_name'] ?? 'Unknown User',
                personalRecord: isset($item['personal_record']) ? (float) $item['personal_record'] : 0.0,
                date: $item['date'] ?? ''
            ), $ranking);
        }

        return new RankingResponseDTO(
            movement: $movementName,
            ranking: $rankingDTOs
        );
    }
}