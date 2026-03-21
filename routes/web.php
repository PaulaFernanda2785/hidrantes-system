<?php

use App\Controllers\AuthController;
use App\Controllers\PainelController;
use App\Controllers\HidranteController;
use App\Controllers\UsuarioController;
use App\Controllers\RelatorioController;
use App\Controllers\HistoricoController;
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;
use App\Middleware\RoleMiddleware;

$router->get('/login', [AuthController::class, 'showLogin'], [GuestMiddleware::class]);
$router->post('/login', [AuthController::class, 'login'], [GuestMiddleware::class]);
$router->get('/logout', [AuthController::class, 'logout'], [AuthMiddleware::class]);

$router->get('/', [PainelController::class, 'index'], [
    AuthMiddleware::class,
    RoleMiddleware::class . ':admin,gestor,operador',
]);

$router->get('/painel', [PainelController::class, 'index'], [
    AuthMiddleware::class,
    RoleMiddleware::class . ':admin,gestor,operador',
]);

$router->get('/hidrantes', [HidranteController::class, 'index'], [
    AuthMiddleware::class,
    RoleMiddleware::class . ':admin,gestor,operador',
]);

$router->get('/hidrantes/novo', [HidranteController::class, 'create'], [
    AuthMiddleware::class,
    RoleMiddleware::class . ':admin,gestor',
]);

$router->post('/hidrantes/salvar', [HidranteController::class, 'store'], [
    AuthMiddleware::class,
    RoleMiddleware::class . ':admin,gestor',
]);

$router->get('/hidrantes/{id}/editar', [HidranteController::class, 'edit'], [
    AuthMiddleware::class,
    RoleMiddleware::class . ':admin,gestor,operador',
]);

$router->post('/hidrantes/{id}/atualizar', [HidranteController::class, 'update'], [
    AuthMiddleware::class,
    RoleMiddleware::class . ':admin,gestor,operador',
]);

$router->post('/hidrantes/{id}/excluir', [HidranteController::class, 'destroy'], [
    AuthMiddleware::class,
    RoleMiddleware::class . ':admin,gestor',
]);

$router->get('/usuarios', [UsuarioController::class, 'index'], [
    AuthMiddleware::class,
    RoleMiddleware::class . ':admin',
]);

$router->get('/usuarios/novo', [UsuarioController::class, 'create'], [
    AuthMiddleware::class,
    RoleMiddleware::class . ':admin',
]);

$router->post('/usuarios/salvar', [UsuarioController::class, 'store'], [
    AuthMiddleware::class,
    RoleMiddleware::class . ':admin',
]);

$router->get('/relatorios/hidrantes', [RelatorioController::class, 'index'], [
    AuthMiddleware::class,
    RoleMiddleware::class . ':admin,gestor',
]);

$router->get('/historico', [HistoricoController::class, 'index'], [
    AuthMiddleware::class,
    RoleMiddleware::class . ':admin,gestor',
]);