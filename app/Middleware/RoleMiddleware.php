<?php

namespace App\Middleware;

use App\Core\Request;
use App\Core\Session;
use App\Core\View;

class RoleMiddleware
{
    public function __construct(private array $roles = [])
    {
    }

    public function handle(Request $request): void
    {
        $auth = Session::get('auth');

        if (!$auth) {
            Session::flash('error', 'Faca login para acessar o sistema.');
            redirect('/login');
        }

        $perfil = $auth['perfil'] ?? null;

        if (!$perfil || ($this->roles && !in_array($perfil, $this->roles, true))) {
            http_response_code(403);
            View::render('errors/403', [
                'title' => 'Acesso negado',
            ]);
            exit;
        }
    }
}
