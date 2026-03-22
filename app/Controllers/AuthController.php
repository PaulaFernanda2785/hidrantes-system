<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\AuthService;
use App\Services\PainelService;

class AuthController extends Controller
{
    public function showLogin(): void
    {
        $painelService = new PainelService();

        $this->view('auth/login', [
            'title' => 'Acesso ao Sistema',
            'metrics' => $painelService->metrics(),
            'mapPoints' => $painelService->mapPoints(),
            'painelPhotoBasePath' => '/painel/fotos/hidrantes',
            'pageStylesheets' => [
                'pages/management.css',
                'pages/painel.css',
            ],
            'headLinks' => [
                'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css',
            ],
            'externalScripts' => [
                'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js',
            ],
            'scripts' => ['pages/painel/index.js'],
        ], 'layouts/auth');
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
