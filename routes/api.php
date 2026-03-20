<?php

use App\Controllers\ApiController;
use App\Middleware\AuthMiddleware;

$router->get('/api/hidrantes/mapa', [ApiController::class, 'mapPoints'], [AuthMiddleware::class]);
$router->get('/api/bairros/municipio/{id}', [ApiController::class, 'bairrosByMunicipio'], [AuthMiddleware::class]);
