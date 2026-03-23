<?php

namespace App\Services;

use App\Repositories\HistoricoRepository;

class HistoricoService
{
    public function __construct(private ?HistoricoRepository $historicoRepository = null)
    {
        $this->historicoRepository ??= new HistoricoRepository();
    }

    public function list(array $filters = [], int $page = 1, int $perPage = 15): array
    {
        return $this->historicoRepository->paginate($filters, $page, $perPage);
    }
}
