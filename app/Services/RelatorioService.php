<?php

namespace App\Services;

use App\Repositories\HidranteRepository;

class RelatorioService
{
    public function __construct(private ?HidranteRepository $hidranteRepository = null)
    {
        $this->hidranteRepository ??= new HidranteRepository();
    }

    public function hidrantes(array $filters = []): array
    {
        return $this->hidranteRepository->report($filters);
    }
}