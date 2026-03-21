<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\AuthService;

class AuthController extends Controller
{
    public function showLogin(): void
    {
        $this->view('auth/login', ['title' => 'Login'], 'layouts/auth');
    }

    public function login(): void
    {
        $matricula = trim((string) $this->request->input('matricula_funcional'));
        $senha = (string) $this->request->input('senha');

        if ($matricula === '' || $senha === '') {
            $this->redirect('/login', null, 'Informe matricula funcional e senha.');
        }

        try {
            $service = new AuthService();
            if (!$service->attempt($matricula, $senha)) {
                $this->redirect('/login', null, 'Credenciais invalidas ou usuario inativo.');
            }
        } catch (\Throwable $e) {
            report_exception($e);
            $this->redirect('/login', null, 'Nao foi possivel concluir o login agora.');
        }

        $this->redirect('/painel', 'Login realizado com sucesso.');
    }

    public function logout(): void
    {
        try {
            (new AuthService())->logout();
        } catch (\Throwable $e) {
            report_exception($e);
        }

        redirect('/login');
    }
}
