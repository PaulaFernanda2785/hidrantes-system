<?php

namespace App\Services;

use App\Repositories\HidranteRepository;

class RelatorioService
{
    private const DOCUMENT_CODE = 'RT-SGH-001';
    private const DOCUMENT_VERSION = '1.0.0';

    public function __construct(private ?HidranteRepository $hidranteRepository = null)
    {
        $this->hidranteRepository ??= new HidranteRepository();
    }

    public function hidrantes(array $filters = []): array
    {
        return $this->hidranteRepository->report($filters);
    }

    public function buildDocument(
        array $items,
        array $filters,
        array $municipios,
        array $bairros,
        string $generatedBy
    ): array {
        $statusOperacional = trim((string) ($filters['status_operacional'] ?? ''));
        $generatedAt = date('d/m/Y H:i');
        $generatedBy = $this->value($generatedBy, 'Sistema');
        $selectedMunicipioNome = $this->lookupNome($municipios, $filters['municipio_id'] ?? null);
        $selectedBairroNome = $this->lookupNome($bairros, $filters['bairro_id'] ?? null);
        $statusMetrics = [
            'total' => count($items),
            'operante' => 0,
            'operante com restricao' => 0,
            'inoperante' => 0,
        ];

        foreach ($items as $item) {
            $currentStatus = strtolower(trim((string) ($item['status_operacional'] ?? '')));

            if (isset($statusMetrics[$currentStatus])) {
                $statusMetrics[$currentStatus]++;
            }
        }

        $filterSummary = [
            'Busca' => $this->value($filters['q'] ?? '', 'Todos os registros'),
            "Status" => $this->statusLabel($statusOperacional, 'Todos'),
            "Munic\u{00ED}pio" => $selectedMunicipioNome,
            'Bairro' => $selectedBairroNome,
            "Emiss\u{00E3}o" => $generatedAt,
            "Respons\u{00E1}vel" => $generatedBy,
        ];

        $reportPages = [
            ['type' => 'cover'],
            ['type' => 'summary'],
        ];

        foreach ($items as $item) {
            $photos = $this->photoItems($item);

            $reportPages[] = [
                'type' => 'hidrante',
                'item' => $item,
                'photos' => $photos,
            ];

            if ($photos !== []) {
                $reportPages[] = [
                    'type' => 'photos',
                    'item' => $item,
                    'photos' => $photos,
                ];
            }
        }

        $totalReportPages = count($reportPages);

        foreach ($reportPages as $index => &$page) {
            $page['page_number'] = $index + 1;
            $page['total_pages'] = $totalReportPages;
        }
        unset($page);

        return [
            'documentCode' => self::DOCUMENT_CODE,
            'documentVersion' => self::DOCUMENT_VERSION,
            'statusOperacional' => $statusOperacional,
            'selectedMunicipioNome' => $selectedMunicipioNome,
            'selectedBairroNome' => $selectedBairroNome,
            'generatedAt' => $generatedAt,
            'generatedBy' => $generatedBy,
            'statusMetrics' => $statusMetrics,
            'filterSummary' => $filterSummary,
            'reportPages' => $reportPages,
            'totalReportPages' => $totalReportPages,
        ];
    }

    private function lookupNome(array $items, string|int|null $id, string $fallback = 'Todos'): string
    {
        $target = trim((string) $id);

        if ($target === '') {
            return $fallback;
        }

        foreach ($items as $item) {
            if ((string) ($item['id'] ?? '') === $target) {
                return (string) ($item['nome'] ?? $fallback);
            }
        }

        return $fallback;
    }

    private function value(mixed $value, string $fallback = "N\u{00E3}o informado"): string
    {
        $normalized = preg_replace('/\s+/u', ' ', trim((string) $value));
        $normalized = is_string($normalized) ? trim($normalized) : '';

        return $normalized !== '' ? $normalized : $fallback;
    }

    private function statusLabel(?string $status, string $fallback = 'Todos'): string
    {
        $normalized = strtolower(trim((string) $status));

        if ($normalized === '') {
            return $fallback;
        }

        return match ($normalized) {
            'operante' => 'Operante',
            'operante com restricao' => "Operante com restri\u{00E7}\u{00E3}o",
            'inoperante' => 'Inoperante',
            default => $this->value($status, $fallback),
        };
    }

    private function photoItems(array $hidrante): array
    {
        $items = [];

        foreach (['foto_01', 'foto_02', 'foto_03'] as $index => $field) {
            $filename = trim((string) ($hidrante[$field] ?? ''));

            if ($filename === '') {
                continue;
            }

            $items[] = [
                'label' => 'Foto ' . ($index + 1),
                'url' => '/uploads/hidrantes/' . rawurlencode($filename),
            ];
        }

        return $items;
    }
}
