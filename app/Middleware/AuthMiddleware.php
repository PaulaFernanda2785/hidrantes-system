<?php

namespace App\Middleware;

use App\Core\Request;
use App\Core\Session;

class AuthMiddleware
{
    public function handle(Request $request): void
    {
        if (!Session::get('auth')) {
            Session::flash('error', 'Faça login para acessar o sistema.');
            redirect('/login');
        }
    }
}
