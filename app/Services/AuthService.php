<?php

namespace App\Services;

use App\Core\Session;
use App\Repositories\UsuarioRepository;

class AuthService
{
    public function __construct(
        private ?UsuarioRepository $usuarioRepository = null,
        private ?PasswordService $passwordService = null,
        private ?AuditService $auditService = null,
    ) {
        $this->usuarioRepository ??= new UsuarioRepository();
        $this->passwordService ??= new PasswordService();
        $this->auditService ??= new AuditService();
    }

    public function attempt(string $matricula, string $senha): bool
    {
        $usuario = $this->usuarioRepository->findByMatricula($matricula);
        if (!$usuario) {
            return false;
        }

        if ($usuario['status'] !== 'ativo') {
            return false;
        }

        if (!$this->passwordService->verify($senha, $usuario['senha_hash'])) {
            return false;
        }

        $auth = [
            'id' => (int) $usuario['id'],
            'nome' => $usuario['nome'],
            'matricula_funcional' => $usuario['matricula_funcional'],
            'perfil' => $usuario['perfil'],
        ];

        Session::put('auth', $auth);
        $this->auditService->record($auth, 'login', 'usuarios', (string) $usuario['id'], 'Login realizado com sucesso.');
        return true;
    }

    public function logout(): void
    {
        $auth = Session::get('auth');
        if ($auth) {
            $this->auditService->record($auth, 'logout', 'usuarios', (string) $auth['id'], 'Logout realizado com sucesso.');
        }
        Session::destroy();
    }
}
