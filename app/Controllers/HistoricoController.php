<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\HistoricoService;

class HistoricoController extends Controller
{
    public function index(): void
    {
        $filters = [
            'usuario_id' => $this->request->input('usuario_id'),
            'acao' => $this->request->input('acao'),
        ];

        $this->view('historico/index', [
            'title' => 'Histórico do Usuário',
            'items' => (new HistoricoService())->list($filters),
            'filters' => $filters,
        ]);
    }
}
