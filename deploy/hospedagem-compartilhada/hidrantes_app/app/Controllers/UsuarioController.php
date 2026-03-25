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
        $nome = trim((string) $this->request->input('nome'));
        $page = (int) ($this->request->input('page') ?: 1);
        $perPage = 15;

        $service = new UsuarioService();
        $result = $service->paginate($nome, $page, $perPage);

        $this->view('usuarios/index', [
            'title' => 'Usuarios',
            'usuarios' => $result['data'],
            'pagination' => $result['meta'],
            'metrics' => $service->metrics(),
            'nome' => $nome,
            'currentUserId' => (int) (Session::get('auth')['id'] ?? 0),
            'scripts' => ['pages/usuarios/index.js'],
        ]);
    }

    public function create(): void
    {
        $this->view('usuarios/create', [
            'title' => 'Novo Usuario',
            'usuario' => null,
            'formAction' => '/usuarios/salvar',
            'isEdit' => false,
        ]);
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

    public function edit(string $id): void
    {
        $usuario = (new UsuarioService())->find((int) $id);
        if (!$usuario) {
            $this->redirect('/usuarios', null, 'Usuario nao encontrado.');
        }

        $this->view('usuarios/create', [
            'title' => 'Editar Usuario',
            'usuario' => $usuario,
            'formAction' => '/usuarios/' . (int) $id . '/atualizar',
            'isEdit' => true,
        ]);
    }

    public function update(string $id): void
    {
        $auth = Session::get('auth');

        try {
            (new UsuarioService())->update((int) $id, $this->request->all(), $auth);
            $this->redirect('/usuarios', 'Usuario atualizado com sucesso.');
        } catch (ValidationException $e) {
            $this->redirect('/usuarios/' . (int) $id . '/editar', null, $e->getMessage());
        } catch (\Throwable $e) {
            report_exception($e);
            $this->redirect('/usuarios/' . (int) $id . '/editar', null, 'Nao foi possivel atualizar o usuario agora.');
        }
    }

    public function myPassword(): void
    {
        $auth = Session::get('auth');
        $id = (int) ($auth['id'] ?? 0);

        if ($id <= 0) {
            $this->redirect('/login', null, 'Faca login para acessar o sistema.');
        }

        $usuario = (new UsuarioService())->find($id);
        if (!$usuario) {
            $this->redirect('/painel', null, 'Usuario nao encontrado.');
        }

        $this->view('usuarios/password', [
            'title' => 'Alterar Minha Senha',
            'usuario' => $usuario,
            'formAction' => '/minha-senha',
            'cancelUrl' => '/painel',
            'isSelfPassword' => true,
        ]);
    }

    public function updateMyPassword(): void
    {
        $auth = Session::get('auth');
        $id = (int) ($auth['id'] ?? 0);

        if ($id <= 0) {
            $this->redirect('/login', null, 'Faca login para acessar o sistema.');
        }

        try {
            (new UsuarioService())->changeOwnPassword($id, $this->request->all(), $auth);
            $this->redirect('/minha-senha', 'Senha alterada com sucesso.');
        } catch (ValidationException $e) {
            $this->redirect('/minha-senha', null, $e->getMessage());
        } catch (\Throwable $e) {
            report_exception($e);
            $this->redirect('/minha-senha', null, 'Nao foi possivel alterar a senha agora.');
        }
    }

    public function password(string $id): void
    {
        $auth = Session::get('auth');
        if ((int) ($auth['id'] ?? 0) === (int) $id) {
            $this->redirect('/minha-senha');
        }

        $usuario = (new UsuarioService())->find((int) $id);
        if (!$usuario) {
            $this->redirect('/usuarios', null, 'Usuario nao encontrado.');
        }

        $this->view('usuarios/password', [
            'title' => 'Alterar Senha do Usuario',
            'usuario' => $usuario,
            'formAction' => '/usuarios/' . (int) $id . '/senha',
            'cancelUrl' => '/usuarios',
            'isSelfPassword' => false,
        ]);
    }

    public function updatePassword(string $id): void
    {
        $auth = Session::get('auth');
        if ((int) ($auth['id'] ?? 0) === (int) $id) {
            $this->redirect('/minha-senha', null, 'Para alterar a propria senha, use esta tela.');
        }

        try {
            (new UsuarioService())->changePassword((int) $id, $this->request->all(), $auth);
            $this->redirect('/usuarios', 'Senha alterada com sucesso.');
        } catch (ValidationException $e) {
            $this->redirect('/usuarios/' . (int) $id . '/senha', null, $e->getMessage());
        } catch (\Throwable $e) {
            report_exception($e);
            $this->redirect('/usuarios/' . (int) $id . '/senha', null, 'Nao foi possivel alterar a senha agora.');
        }
    }

    public function updateStatus(string $id): void
    {
        $auth = Session::get('auth');
        $status = (string) $this->request->input('status');

        try {
            (new UsuarioService())->updateStatus((int) $id, $status, $auth);
            $message = $status === 'ativo'
                ? 'Usuario ativado com sucesso.'
                : 'Usuario inativado com sucesso.';
            $this->redirect('/usuarios', $message);
        } catch (ValidationException $e) {
            $this->redirect('/usuarios', null, $e->getMessage());
        } catch (\Throwable $e) {
            report_exception($e);
            $this->redirect('/usuarios', null, 'Nao foi possivel alterar o status do usuario agora.');
        }
    }
}
