<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\PainelService;

class PainelController extends Controller
{
    public function index(): void
    {
        $service = new PainelService();
        $this->view('painel/index', [
            'title' => 'Painel Operacional',
            'metrics' => $service->metrics(),
            'mapPoints' => $service->mapPoints(),
        ]);
    }
}
