<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Application\Service\RankingService;
use App\Core\Router;
use App\Config\Database;
use App\Http\Response;
use App\Infrastructure\Repository\RankingRepository;
use App\Middleware\AuthMiddleware;
use App\Presentation\Controller\RankingController;

// Carrega variáveis de ambiente
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

AuthMiddleware::handle();

// Handler global de exceções
set_exception_handler(function (\Throwable $e) {
    // Determina status code
    $status = method_exists($e, 'getCode') && $e->getCode() >= 400 && $e->getCode() < 600
        ? $e->getCode()
        : 500;

    $message = $status === 404 ? 'Recurso não encontrado' : 'Erro interno do servidor! Tente novamente mais tarde.';

    Response::error($message, $status);
});

// Instancia o Router
$router = new Router();

$route = $router->resolve(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI']
);

// Rota não encontrada
if ($route['controller'] === null) {
    Response::error('Rota não encontrada', 404);
}

// Instancia dependências
$database   = new Database();
$repository = new RankingRepository($database->getConnection());
$service    = new RankingService($repository);
$controller = new RankingController($service);

// Executa controller
if ($route['controller'] === 'ranking') {
    $controller->handle($route['movement_id'] ?? null, $route['movement_name'] ?? null);
}