<?php

namespace App\Middleware;

use App\Core\Request;
use App\Core\Session;

class RoleMiddleware
{
    public function __construct(private array $roles = [])
    {
    }

    public function handle(Request $request): void
    {
        $auth = Session::get('auth');
        if (!$auth) {
            Session::flash('error', 'Faça login para acessar o sistema.');
            redirect('/login');
        }

        if ($this->roles && !in_array($auth['perfil'], $this->roles, true)) {
            http_response_code(403);
            echo '403 - Acesso negado';
            exit;
        }
    }
}
