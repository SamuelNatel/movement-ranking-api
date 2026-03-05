<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Application\Service\RankingService;
use DomainException;

class RankingController
{
    public function __construct(
        private RankingService $service
    ) {}

    public function handle(?int $movementId, ?string $movementName): void
    {
        header('Content-Type: application/json');

        try {
            $response = $this->service->getRanking($movementId, $movementName);
            echo json_encode($response);
        } catch (DomainException $e) {
            // Se o código da exceção não for 404 ou 400, padrão para 500
            $code = in_array($e->getCode(), [400, 404]) ? $e->getCode() : 500;
            http_response_code($code);
            echo json_encode(['error' => $e->getMessage()]);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Erro interno do servidor! Tente novamente mais tarde.']);
        }
    }
}