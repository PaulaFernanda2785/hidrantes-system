<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\HistoricoService;

class HistoricoController extends Controller
{
    public function index(): void
    {
        $filters = [
            'usuario_nome' => trim((string) $this->request->input('usuario_nome')),
            'acao' => trim((string) $this->request->input('acao')),
        ];

        $page = (int) ($this->request->input('page') ?: 1);
        $perPage = 15;

        $result = (new HistoricoService())->list($filters, $page, $perPage);

        $this->view('historico/index', [
            'title' => 'Historico do Usuario',
            'items' => $result['data'],
            'pagination' => $result['meta'],
            'filters' => $filters,
            'scripts' => ['pages/historico/index.js'],
        ]);
    }
}
