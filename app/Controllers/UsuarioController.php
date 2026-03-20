<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Services\UsuarioService;

class UsuarioController extends Controller
{
    public function index(): void
    {
        $nome = $this->request->input('nome');
        $this->view('usuarios/index', [
            'title' => 'Usuários',
            'usuarios' => (new UsuarioService())->list($nome),
            'nome' => $nome,
        ]);
    }

    public function create(): void
    {
        $this->view('usuarios/create', ['title' => 'Novo Usuário']);
    }

    public function store(): void
    {
        $auth = Session::get('auth');
        $required = ['nome', 'matricula_funcional', 'senha', 'perfil', 'status'];
        foreach ($required as $field) {
            if (trim((string) $this->request->input($field)) === '') {
                $this->redirect('/usuarios/novo', null, 'Preencha todos os campos obrigatórios do usuário.');
            }
        }

        try {
            (new UsuarioService())->create($this->request->all(), $auth);
            $this->redirect('/usuarios', 'Usuário cadastrado com sucesso.');
        } catch (\Throwable $e) {
            $this->redirect('/usuarios/novo', null, $e->getMessage());
        }
    }
}
