<?php

namespace App\Services;

use App\Repositories\BairroRepository;
use App\Validators\ValidationException;

class BairroService
{
    public function __construct(
        private ?BairroRepository $bairroRepository = null,
        private ?AuditService $auditService = null,
    ) {
        $this->bairroRepository ??= new BairroRepository();
        $this->auditService ??= new AuditService();
    }

    public function create(array $data, array $actor): array
    {
        $payload = $this->validate($data);
        $existing = $this->bairroRepository->findByMunicipioAndNome($payload['municipio_id'], $payload['nome']);

        if ($existing) {
            $bairro = $this->bairroRepository->findById((int) $existing['id']);

            if ((int) ($existing['ativo'] ?? 1) !== 1) {
                $this->bairroRepository->reactivate((int) $existing['id']);

                if ($bairro) {
                    $this->recordAuditSafely(
                        $actor,
                        'editar',
                        'bairros',
                        (string) $bairro['id'],
                        sprintf(
                            'Reativacao do bairro %s vinculada ao municipio %s.',
                            $bairro['nome'],
                            $bairro['municipio_nome']
                        )
                    );
                }
            }

            return [
                'id' => (int) ($bairro['id'] ?? $existing['id']),
                'nome' => (string) ($bairro['nome'] ?? $existing['nome']),
                'municipio_id' => (int) ($bairro['municipio_id'] ?? $payload['municipio_id']),
                'message' => 'Bairro ja existente e agora disponivel na lista.',
            ];
        }

        $bairroId = $this->bairroRepository->create($payload);
        $bairro = $this->bairroRepository->findById($bairroId);

        $this->recordAuditSafely(
            $actor,
            'cadastrar',
            'bairros',
            (string) $bairroId,
            sprintf(
                'Cadastro de bairro %s vinculado ao municipio %s.',
                $payload['nome'],
                $bairro['municipio_nome'] ?? 'nao informado'
            )
        );

        return [
            'id' => $bairroId,
            'nome' => $payload['nome'],
            'municipio_id' => $payload['municipio_id'],
            'message' => 'Bairro cadastrado com sucesso.',
        ];
    }

    public function update(int $id, array $data, array $actor): array
    {
        $current = $this->bairroRepository->findById($id);

        if (!$current || (int) ($current['ativo'] ?? 0) !== 1) {
            throw new ValidationException([
                'bairro_id' => 'Bairro nao encontrado.',
            ]);
        }

        $payload = $this->validate($data);

        if ((int) $current['municipio_id'] !== $payload['municipio_id']) {
            throw new ValidationException([
                'municipio_id' => 'O bairro selecionado nao pertence ao municipio informado.',
            ]);
        }

        $duplicate = $this->bairroRepository->findByMunicipioAndNome(
            $payload['municipio_id'],
            $payload['nome'],
            $id
        );

        if ($duplicate) {
            throw new ValidationException([
                'nome' => 'Ja existe outro bairro com esse nome para este municipio.',
            ]);
        }

        if (mb_strtolower($current['nome']) === mb_strtolower($payload['nome'])) {
            return [
                'id' => (int) $current['id'],
                'nome' => (string) $current['nome'],
                'municipio_id' => (int) $current['municipio_id'],
                'message' => 'Nenhuma alteracao foi realizada no bairro.',
            ];
        }

        $this->bairroRepository->update($id, $payload);

        $this->recordAuditSafely(
            $actor,
            'editar',
            'bairros',
            (string) $id,
            sprintf(
                'Edicao de bairro realizada. Nome anterior: %s. Nome atual: %s. Municipio: %s.',
                $current['nome'],
                $payload['nome'],
                $current['municipio_nome']
            )
        );

        return [
            'id' => $id,
            'nome' => $payload['nome'],
            'municipio_id' => $payload['municipio_id'],
            'message' => 'Bairro atualizado com sucesso.',
        ];
    }

    private function validate(array $data): array
    {
        $municipioId = (int) ($data['municipio_id'] ?? 0);
        $nome = $this->normalizePlainText((string) ($data['nome'] ?? ''));
        $errors = [];

        if ($municipioId <= 0) {
            $errors['municipio_id'] = 'Selecione um municipio valido antes de cadastrar o bairro.';
        }

        if ($nome === '') {
            $errors['nome'] = 'Informe o nome do bairro.';
        }

        if ($nome !== '' && mb_strlen($nome) > 150) {
            $errors['nome'] = 'O nome do bairro excede 150 caracteres.';
        }

        if ($errors) {
            throw new ValidationException($errors);
        }

        return [
            'municipio_id' => $municipioId,
            'nome' => $nome,
        ];
    }

    private function normalizePlainText(string $value): string
    {
        $normalized = preg_replace('/[\x00-\x1F\x7F]+/u', ' ', $value);

        return trim(preg_replace('/\s+/u', ' ', (string) $normalized) ?? '');
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
