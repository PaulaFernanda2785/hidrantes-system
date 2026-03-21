<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\MunicipioRepository;
use App\Services\RelatorioService;

class RelatorioController extends Controller
{
    public function index(): void
    {
        $filters = [
            'status_operacional' => $this->request->input('status_operacional'),
            'municipio_id' => $this->request->input('municipio_id'),
        ];

        $this->view('relatorios/index', [
            'title' => 'Relatorio de Hidrantes',
            'items' => (new RelatorioService())->hidrantes($filters),
            'municipios' => (new MunicipioRepository())->all(),
            'filters' => $filters,
        ]);
    }
}
