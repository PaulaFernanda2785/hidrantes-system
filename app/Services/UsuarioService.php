<?php

namespace App\Services;

use App\Repositories\UsuarioRepository;

class UsuarioService
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

    public function list(?string $nome = null): array
    {
        return $this->usuarioRepository->all($nome);
    }

    public function create(array $data, array $actor): int
    {
        $id = $this->usuarioRepository->create([
            'nome' => trim((string) $data['nome']),
            'matricula_funcional' => trim((string) $data['matricula_funcional']),
            'senha_hash' => $this->passwordService->hash((string) $data['senha']),
            'perfil' => $data['perfil'],
            'status' => $data['status'],
        ]);

        $this->auditService->record($actor, 'cadastrar', 'usuarios', (string) $id, 'Cadastro de usuário realizado.');
        return $id;
    }
}
