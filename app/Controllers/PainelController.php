<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\PainelService;

class PainelController extends Controller
{
    public function index(): void
    {
        $service = new PainelService();
        $mapPoints = $service->mapPoints();

        $this->view('painel/index', [
            'title' => 'Painel Operacional',
            'metrics' => $service->metrics(),
            'mapPoints' => $mapPoints,
            'painelPhotoBasePath' => '/painel/fotos/hidrantes',
            'pageStylesheets' => ['pages/painel.css'],
            'headLinks' => [
                'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css',
            ],
            'externalScripts' => [
                'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js',
            ],
            'scripts' => ['pages/painel/index.js'],
        ]);
    }
}
