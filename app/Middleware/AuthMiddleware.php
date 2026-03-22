<?php

namespace App\Middleware;

use App\Core\Request;
use App\Core\Session;

class AuthMiddleware
{
    public function handle(Request $request): void
    {
        $auth = Session::get('auth');

        if (!$auth) {
            Session::flash('error', 'Faça login para acessar o sistema.');
            redirect('/login');
        }

        $context = Session::get('_auth_context', []);
        $expectedFingerprint = auth_session_fingerprint();
        $fingerprint = (string) ($context['fingerprint'] ?? '');
        $lastActivity = (int) ($context['last_activity'] ?? 0);
        $maxIdleSeconds = (int) config('session.lifetime', 7200);

        if ($fingerprint === '' || !hash_equals($fingerprint, $expectedFingerprint)) {
            Session::invalidate('Sua sessão foi encerrada por segurança. Faça login novamente.');
            redirect('/login');
        }

        if ($lastActivity > 0 && (time() - $lastActivity) > $maxIdleSeconds) {
            Session::invalidate('Sua sessão expirou por inatividade. Faça login novamente.');
            redirect('/login');
        }

        Session::put('_auth_context', [
            'fingerprint' => $expectedFingerprint,
            'last_activity' => time(),
        ]);
    }
}
