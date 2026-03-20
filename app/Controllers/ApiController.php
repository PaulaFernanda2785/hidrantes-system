<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;
use App\Repositories\BairroRepository;
use App\Services\PainelService;

class ApiController extends Controller
{
    public function mapPoints(): void
    {
        Response::json((new PainelService())->mapPoints());
    }

    public function bairrosByMunicipio(string $municipioId): void
    {
        Response::json((new BairroRepository())->byMunicipio((int) $municipioId));
    }
}
