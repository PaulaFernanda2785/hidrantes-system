<?php

namespace App\Middleware;

use App\Core\Request;
use App\Core\Session;

class GuestMiddleware
{
    public function handle(Request $request): void
    {
        if (Session::get('auth')) {
            redirect('/painel');
        }
    }
}
