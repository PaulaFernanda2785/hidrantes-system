<?php

namespace App\Services;

use App\Repositories\HidranteRepository;

class HidranteService
{
    public function __construct(
        private ?HidranteRepository $hidranteRepository = null,
        private ?UploadService $uploadService = null,
        private ?GeoService $geoService = null,
        private ?AuditService $auditService = null,
    ) {
        $this->hidranteRepository ??= new HidranteRepository();
        $this->uploadService ??= new UploadService();
        $this->geoService ??= new GeoService();
        $this->auditService ??= new AuditService();
    }

    public function list(array $filters = []): array
    {
        return $this->hidranteRepository->all($filters);
    }

    public function create(array $data, array $files, array $actor): int
    {
        if ($this->hidranteRepository->existsByNumero($data['numero_hidrante'])) {
            throw new \InvalidArgumentException('Já existe um hidrante com esse número.');
        }

        $images = $this->uploadService->storeMultiple($files);

        $payload = [
            'numero_hidrante' => trim((string) $data['numero_hidrante']),
            'equipe_responsavel' => trim((string) $data['equipe_responsavel']),
            'area' => $data['area'],
            'existe_no_local' => $data['existe_no_local'],
            'tipo_hidrante' => $data['tipo_hidrante'],
            'acessibilidade' => $data['acessibilidade'],
            'tampo_conexoes' => $data['tampo_conexoes'],
            'tampas_ausentes' => $data['tampas_ausentes'] ?: null,
            'caixa_protecao' => $data['caixa_protecao'],
            'condicao_caixa' => $data['condicao_caixa'] ?: null,
            'presenca_agua_interior' => $data['presenca_agua_interior'],
            'teste_realizado' => $data['teste_realizado'],
            'resultado_teste' => $data['resultado_teste'] ?: null,
            'status_operacional' => $data['status_operacional'],
            'municipio_id' => (int) $data['municipio_id'],
            'bairro_id' => !empty($data['bairro_id']) ? (int) $data['bairro_id'] : null,
            'endereco' => trim((string) $data['endereco']),
            'latitude' => $this->geoService->isValid($data['latitude'] ?? null, $data['longitude'] ?? null) ? $data['latitude'] : null,
            'longitude' => $this->geoService->isValid($data['latitude'] ?? null, $data['longitude'] ?? null) ? $data['longitude'] : null,
            'foto_01' => $images['foto_01'],
            'foto_02' => $images['foto_02'],
            'foto_03' => $images['foto_03'],
            'criado_por_usuario_id' => $actor['id'],
            'atualizado_por_usuario_id' => $actor['id'],
        ];

        $id = $this->hidranteRepository->create($payload);
        $this->auditService->record($actor, 'cadastrar', 'hidrantes', (string) $id, 'Cadastro de hidrante realizado.');
        return $id;
    }

    public function mapPoints(): array
    {
        return $this->hidranteRepository->mapPoints();
    }
}
