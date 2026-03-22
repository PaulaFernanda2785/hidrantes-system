<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\BairroRepository;
use App\Repositories\MunicipioRepository;
use App\Services\RelatorioService;

class RelatorioController extends Controller
{
    public function index(): void
    {
        $filters = [
            'q' => trim((string) $this->request->input('q')),
            'status_operacional' => $this->request->input('status_operacional'),
            'municipio_id' => $this->request->input('municipio_id'),
            'bairro_id' => $this->request->input('bairro_id'),
        ];

        $bairros = [];
        if (!empty($filters['municipio_id'])) {
            $bairros = (new BairroRepository())->byMunicipio((int) $filters['municipio_id']);
        }

        $this->view('relatorios/index', [
            'title' => 'Relatorio de Hidrantes',
            'items' => (new RelatorioService())->hidrantes($filters),
            'municipios' => (new MunicipioRepository())->all(),
            'bairros' => $bairros,
            'filters' => $filters,
            'scripts' => ['pages/relatorios/index.js'],
        ]);
    }
}
