<?php

namespace App\Services;

use App\Repositories\HidranteRepository;

class PainelService
{
    public function __construct(private ?HidranteRepository $hidranteRepository = null)
    {
        $this->hidranteRepository ??= new HidranteRepository();
    }

    public function metrics(): array
    {
        return $this->hidranteRepository->metrics();
    }

    public function mapPoints(): array
    {
        return $this->hidranteRepository->mapPoints();
    }
}
