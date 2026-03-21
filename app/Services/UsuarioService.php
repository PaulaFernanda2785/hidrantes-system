<?php

namespace App\Services;

use App\Core\Session;
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

    public function metrics(): array
    {
        return $this->usuarioRepository->metrics();
    }

    public function paginate(?string $nome = null, int $page = 1, int $perPage = 15): array
    {
        return $this->usuarioRepository->paginate($nome, $page, $perPage);
    }

    public function find(int $id): ?array
    {
        return $this->usuarioRepository->findById($id);
    }

    public function create(array $data, array $actor): int
    {
        $payload = $this->validateAndNormalize($data, true);
        $payload['senha_hash'] = $this->passwordService->hash((string) $data['senha']);

        $id = $this->usuarioRepository->create($payload);

        $this->recordAuditSafely($actor, 'cadastrar', 'usuarios', (string) $id, 'Cadastro de usuario realizado.');

        return $id;
    }

    public function update(int $id, array $data, array $actor): void
    {
        $current = $this->usuarioRepository->findById($id);
        if (!$current) {
            throw new \RuntimeException('Usuario nao encontrado.');
        }

        $payload = $this->validateAndNormalize($data, false, $id);

        if ((int) $actor['id'] === $id) {
            if (($payload['perfil'] ?? '') !== ($current['perfil'] ?? '')) {
                throw new ValidationException(['perfil' => 'Nao altere o proprio perfil por esta tela.']);
            }

            if (($payload['status'] ?? '') !== ($current['status'] ?? '')) {
                throw new ValidationException(['status' => 'Nao altere o proprio status por esta tela.']);
            }
        }

        $this->usuarioRepository->update($id, $payload);

        if ((int) $actor['id'] === $id) {
            Session::put('auth', [
                'id' => $id,
                'nome' => $payload['nome'],
                'matricula_funcional' => $payload['matricula_funcional'],
                'perfil' => $current['perfil'],
            ]);
        }

        $this->recordAuditSafely($actor, 'editar', 'usuarios', (string) $id, 'Edicao de usuario realizada.');
    }

    public function changePassword(int $id, array $data, array $actor): void
    {
        $current = $this->usuarioRepository->findById($id);
        if (!$current) {
            throw new \RuntimeException('Usuario nao encontrado.');
        }

        $newPassword = trim((string) ($data['nova_senha'] ?? ''));
        $confirmation = trim((string) ($data['confirmacao_senha'] ?? ''));

        if ($newPassword === '') {
            throw new ValidationException(['nova_senha' => 'Informe a nova senha.']);
        }

        if (mb_strlen($newPassword) < 6) {
            throw new ValidationException(['nova_senha' => 'A nova senha deve ter pelo menos 6 caracteres.']);
        }

        if ($newPassword !== $confirmation) {
            throw new ValidationException(['confirmacao_senha' => 'A confirmacao de senha nao confere.']);
        }

        $this->usuarioRepository->updatePassword($id, $this->passwordService->hash($newPassword));
        $this->recordAuditSafely($actor, 'alterar senha', 'usuarios', (string) $id, 'Senha do usuario alterada.');
    }

    public function updateStatus(int $id, string $status, array $actor): void
    {
        $current = $this->usuarioRepository->findById($id);
        if (!$current) {
            throw new \RuntimeException('Usuario nao encontrado.');
        }

        $status = trim($status);
        if (!in_array($status, ['ativo', 'inativo'], true)) {
            throw new ValidationException(['status' => 'Status invalido.']);
        }

        if ((int) $actor['id'] === $id) {
            throw new ValidationException(['status' => 'Nao altere o proprio status por esta tela.']);
        }

        if (($current['status'] ?? null) === $status) {
            throw new ValidationException(['status' => 'O usuario ja esta com esse status.']);
        }

        $this->usuarioRepository->updateStatus($id, $status);

        $acao = $status === 'ativo' ? 'ativar' : 'inativar';
        $detalhes = $status === 'ativo'
            ? 'Usuario ativado.'
            : 'Usuario inativado.';

        $this->recordAuditSafely($actor, $acao, 'usuarios', (string) $id, $detalhes);
    }

    private function validateAndNormalize(array $data, bool $requirePassword = false, ?int $ignoreId = null): array
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
        } elseif ($this->usuarioRepository->existsByMatricula($matricula, $ignoreId)) {
            $errors['matricula_funcional'] = 'Ja existe um usuario com essa matricula.';
        }

        if ($requirePassword && trim($senha) === '') {
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

    private function recordAuditSafely(array $actor, string $acao, string $entidade, string $referencia, string $detalhes): void
    {
        try {
            $this->auditService->record($actor, $acao, $entidade, $referencia, $detalhes);
        } catch (\Throwable $e) {
            report_exception($e);
        }
    }
}
