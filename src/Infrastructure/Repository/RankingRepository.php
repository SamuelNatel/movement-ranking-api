<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use PDO;

class RankingRepository
{
    public function __construct(
        private PDO $connection
    ) {}

    /**
     * Retorna o ranking do movimento pelo ID.
     * Se não houver registros, retorna array vazio.
     */
    public function findRankingByMovementId(int $movementId): array
    {
        $sql = "
            WITH best_records AS (
                SELECT 
                    u.id AS user_id,
                    u.name AS user_name,
                    m.name AS movement_name,
                    MAX(pr.value) AS personal_record
                FROM movement m
                LEFT JOIN personal_record pr 
                    ON pr.movement_id = m.id
                LEFT JOIN user u 
                    ON u.id = pr.user_id
                WHERE m.id = :movement_id
                GROUP BY u.id, u.name, m.name
            )
            SELECT 
                br.user_name,
                br.movement_name,
                br.personal_record,
                pr.date,
                RANK() OVER (
                    ORDER BY br.personal_record DESC
                ) AS position
            FROM best_records br
            LEFT JOIN personal_record pr 
                ON pr.user_id = br.user_id 
               AND pr.value = br.personal_record
            WHERE br.personal_record IS NOT NULL
            ORDER BY position;
        ";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['movement_id' => $movementId]);

        return $stmt->fetchAll();
    }

    /**
     * Retorna o ranking do movimento pelo nome.
     * Se o movimento não existir, retorna array vazio.
     */
    public function findRankingByMovementName(string $movementName): array
    {
        $stmt = $this->connection->prepare(
            'SELECT id FROM movement WHERE name = :name'
        );
        $stmt->execute(['name' => $movementName]);
        $movement = $stmt->fetch();

        if (!$movement) {
            return [];
        }

        return $this->findRankingByMovementId((int)$movement['id']);
    }

    /**
     * Retorna o nome do movimento pelo ID.
     */
    public function findMovementNameById(int $movementId): string
    {
        $stmt = $this->connection->prepare(
            'SELECT name FROM movement WHERE id = :id'
        );
        $stmt->execute(['id' => $movementId]);
        $result = $stmt->fetch();

        return $result['name'] ?? 'Unknown Movement';
    }

    /**
     * Retorna o nome do movimento pelo nome (para validação).
     */
    public function findMovementNameByName(string $movementName): string
    {
        $stmt = $this->connection->prepare(
            'SELECT name FROM movement WHERE name = :name'
        );
        $stmt->execute(['name' => $movementName]);
        $result = $stmt->fetch();

        return $result['name'] ?? 'Unknown Movement';
    }
}