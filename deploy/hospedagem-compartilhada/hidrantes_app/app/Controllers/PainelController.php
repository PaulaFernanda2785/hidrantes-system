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
            'showManualButton' => true,
            'manualUrl' => '/painel/manual/usuario',
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

    public function manualUsuario(): void
    {
        $path = base_path('documentos/manual-do-usuario.html');

        if (!is_file($path)) {
            http_response_code(404);
            echo 'Manual do usuario nao encontrado.';
            return;
        }

        $content = file_get_contents($path);

        if ($content === false) {
            http_response_code(500);
            echo 'Nao foi possivel carregar o manual do usuario.';
            return;
        }

        $content = str_replace('../public/img/logos/logo.cbmpa.png', '/img/logos/logo.cbmpa.png', $content);

        header('Content-Type: text/html; charset=utf-8');
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo $content;
    }
}

