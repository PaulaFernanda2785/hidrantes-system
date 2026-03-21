<?php

namespace App\Services;

use App\Repositories\UsuarioRepository;
use App\Validators\ValidationException;

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
        $payload = $this->validateAndNormalize($data);
        $payload['senha_hash'] = $this->passwordService->hash((string) $data['senha']);

        $id = $this->usuarioRepository->create($payload);

        try {
            $this->auditService->record($actor, 'cadastrar', 'usuarios', (string) $id, 'Cadastro de usuario realizado.');
        } catch (\Throwable $e) {
            report_exception($e);
        }

        return $id;
    }

    private function validateAndNormalize(array $data): array
    {
        $errors = [];

        $nome = trim((string) ($data['nome'] ?? ''));
        $matricula = trim((string) ($data['matricula_funcional'] ?? ''));
        $senha = (string) ($data['senha'] ?? '');
        $perfil = trim((string) ($data['perfil'] ?? ''));
        $status = trim((string) ($data['status'] ?? ''));

        if ($nome === '') {
            $errors['nome'] = 'Informe o nome do usuario.';
        } elseif (mb_strlen($nome) > 150) {
            $errors['nome'] = 'O nome do usuario excede 150 caracteres.';
        }

        if ($matricula === '') {
            $errors['matricula_funcional'] = 'Informe a matricula funcional.';
        } elseif (mb_strlen($matricula) > 30) {
            $errors['matricula_funcional'] = 'A matricula funcional excede 30 caracteres.';
        } elseif ($this->usuarioRepository->existsByMatricula($matricula)) {
            $errors['matricula_funcional'] = 'Ja existe um usuario com essa matricula.';
        }

        if (trim($senha) === '') {
            $errors['senha'] = 'Informe a senha do usuario.';
        }

        if (!in_array($perfil, ['admin', 'gestor', 'operador'], true)) {
            $errors['perfil'] = 'Perfil invalido.';
        }

        if (!in_array($status, ['ativo', 'inativo'], true)) {
            $errors['status'] = 'Status invalido.';
        }

        if ($errors) {
            throw new ValidationException($errors, 'Verifique os campos do usuario.');
        }

        return [
            'nome' => $nome,
            'matricula_funcional' => $matricula,
            'perfil' => $perfil,
            'status' => $status,
        ];
    }
}
