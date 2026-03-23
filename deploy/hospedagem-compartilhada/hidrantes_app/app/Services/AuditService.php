<?php

namespace App\Services;

use App\Repositories\HistoricoRepository;

class AuditService
{
    public function __construct(private ?HistoricoRepository $historicoRepository = null)
    {
        $this->historicoRepository ??= new HistoricoRepository();
    }

    public function record(array $actor, string $acao, ?string $entidade = null, ?string $referencia = null, ?string $detalhes = null): void
    {
        $this->historicoRepository->create([
            'usuario_id' => $actor['id'],
            'usuario_nome_snapshot' => $actor['nome'],
            'acao' => $acao,
            'entidade' => $entidade,
            'referencia_registro' => $referencia,
            'detalhes' => $detalhes,
            'ip_origem' => client_ip(),
        ]);
    }
}
