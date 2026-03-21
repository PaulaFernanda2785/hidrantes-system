<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Services\UsuarioService;
use App\Validators\ValidationException;

class UsuarioController extends Controller
{
    public function index(): void
    {
        $nome = $this->request->input('nome');
        $this->view('usuarios/index', [
            'title' => 'Usuarios',
            'usuarios' => (new UsuarioService())->list($nome),
            'nome' => $nome,
        ]);
    }

    public function create(): void
    {
        $this->view('usuarios/create', ['title' => 'Novo Usuario']);
    }

    public function store(): void
    {
        $auth = Session::get('auth');
        $required = ['nome', 'matricula_funcional', 'senha', 'perfil', 'status'];

        foreach ($required as $field) {
            if (trim((string) $this->request->input($field)) === '') {
                $this->redirect('/usuarios/novo', null, 'Preencha todos os campos obrigatorios do usuario.');
            }
        }

        try {
            (new UsuarioService())->create($this->request->all(), $auth);
            $this->redirect('/usuarios', 'Usuario cadastrado com sucesso.');
        } catch (ValidationException $e) {
            $this->redirect('/usuarios/novo', null, $e->getMessage());
        } catch (\Throwable $e) {
            report_exception($e);
            $this->redirect('/usuarios/novo', null, 'Nao foi possivel cadastrar o usuario agora.');
        }
    }
}
