<?php

use App\Controllers\AuthController;
use App\Controllers\PainelController;
use App\Controllers\HidranteController;
use App\Controllers\UsuarioController;
use App\Controllers\RelatorioController;
use App\Controllers\HistoricoController;
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;

$router->get('/login', [AuthController::class, 'showLogin'], [GuestMiddleware::class]);
$router->post('/login', [AuthController::class, 'login'], [GuestMiddleware::class]);
$router->get('/logout', [AuthController::class, 'logout'], [AuthMiddleware::class]);

$router->get('/', [PainelController::class, 'index'], [AuthMiddleware::class]);
$router->get('/painel', [PainelController::class, 'index'], [AuthMiddleware::class]);

$router->get('/hidrantes', [HidranteController::class, 'index'], [AuthMiddleware::class]);
$router->get('/hidrantes/novo', [HidranteController::class, 'create'], [AuthMiddleware::class]);
$router->post('/hidrantes/salvar', [HidranteController::class, 'store'], [AuthMiddleware::class]);

$router->get('/usuarios', [UsuarioController::class, 'index'], [AuthMiddleware::class]);
$router->get('/usuarios/novo', [UsuarioController::class, 'create'], [AuthMiddleware::class]);
$router->post('/usuarios/salvar', [UsuarioController::class, 'store'], [AuthMiddleware::class]);

$router->get('/relatorios/hidrantes', [RelatorioController::class, 'index'], [AuthMiddleware::class]);
$router->get('/historico', [HistoricoController::class, 'index'], [AuthMiddleware::class]);
