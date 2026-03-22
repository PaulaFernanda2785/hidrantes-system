<?php
$metrics = $metrics ?? [
    'total' => 0,
    'operantes' => 0,
    'restricao' => 0,
    'inoperantes' => 0,
];
$mapPoints = $mapPoints ?? [];
$painelPhotoBasePath = $painelPhotoBasePath ?? '/painel/fotos/hidrantes';

if (!function_exists('painel_value')) {
    function painel_value(mixed $value, string $fallback = 'Não informado'): string
    {
        $normalized = trim((string) $value);

        return $normalized !== '' ? $normalized : $fallback;
    }
}

if (!function_exists('painel_status_class')) {
    function painel_status_class(?string $status): string
    {
        return match (strtolower(trim((string) $status))) {
            'operante' => 'is-operante',
            'operante com restricao' => 'is-restricao',
            'inoperante' => 'is-inoperante',
            default => 'is-neutral',
        };
    }
}

if (!function_exists('painel_format_datetime')) {
    function painel_format_datetime(?string $value): string
    {
        $normalized = trim((string) $value);

        if ($normalized === '') {
            return 'Não informado';
        }

        $timestamp = strtotime($normalized);

        return $timestamp === false ? $normalized : date('d/m/Y H:i', $timestamp);
    }
}

if (!function_exists('painel_photo_items')) {
    function painel_photo_items(array $hidrante, string $basePath): array
    {
        $items = [];

        foreach (['foto_01', 'foto_02', 'foto_03'] as $index => $field) {
            $filename = trim((string) ($hidrante[$field] ?? ''));

            if ($filename === '') {
                continue;
            }

            $items[] = [
                'label' => 'Foto ' . ($index + 1),
                'url' => rtrim($basePath, '/') . '/' . rawurlencode($filename),
            ];
        }

        return $items;
    }
}

$mapPointCount = count($mapPoints);
$withoutCoordinates = max(0, (int) ($metrics['total'] ?? 0) - $mapPointCount);

$mapPayload = array_map(static function (array $item) use ($painelPhotoBasePath): array {
    return [
        'id' => (int) ($item['id'] ?? 0),
        'numero_hidrante' => painel_value($item['numero_hidrante'] ?? ''),
        'equipe_responsavel' => painel_value($item['equipe_responsavel'] ?? ''),
        'area' => painel_value($item['area'] ?? ''),
        'existe_no_local' => painel_value($item['existe_no_local'] ?? ''),
        'tipo_hidrante' => painel_value($item['tipo_hidrante'] ?? ''),
        'status_operacional' => painel_value($item['status_operacional'] ?? ''),
        'acessibilidade' => painel_value($item['acessibilidade'] ?? ''),
        'tampo_conexoes' => painel_value($item['tampo_conexoes'] ?? ''),
        'tampas_ausentes' => painel_value($item['tampas_ausentes'] ?? ''),
        'caixa_protecao' => painel_value($item['caixa_protecao'] ?? ''),
        'condicao_caixa' => painel_value($item['condicao_caixa'] ?? ''),
        'presenca_agua_interior' => painel_value($item['presenca_agua_interior'] ?? ''),
        'teste_realizado' => painel_value($item['teste_realizado'] ?? ''),
        'resultado_teste' => painel_value($item['resultado_teste'] ?? ''),
        'municipio_nome' => painel_value($item['municipio_nome'] ?? ''),
        'bairro_nome' => painel_value($item['bairro_nome'] ?? ''),
        'endereco' => painel_value($item['endereco'] ?? ''),
        'latitude' => trim((string) ($item['latitude'] ?? '')),
        'longitude' => trim((string) ($item['longitude'] ?? '')),
        'criado_em' => painel_format_datetime($item['criado_em'] ?? ''),
        'atualizado_em' => painel_format_datetime($item['atualizado_em'] ?? ''),
        'fotos' => painel_photo_items($item, $painelPhotoBasePath),
    ];
}, $mapPoints);

$mapPayloadJson = json_encode(
    $mapPayload,
    JSON_UNESCAPED_UNICODE
    | JSON_UNESCAPED_SLASHES
    | JSON_HEX_TAG
    | JSON_HEX_AMP
    | JSON_HEX_APOS
    | JSON_HEX_QUOT
);
?>

<div class="management-page painel-page">
    <section class="card management-card management-hero">
        <div class="management-header">
            <div class="management-header-copy">
                <p class="management-eyebrow">Monitoramento em campo</p>
                <h1><?= e($title ?? 'Painel Operacional') ?></h1>
                <p class="management-description">
                    Acompanhe a distribuição dos hidrantes georreferenciados no mapa, identifique rapidamente a situação operacional de cada ponto e abra a ficha completa do hidrante com rota direta para deslocamento.
                </p>
            </div>
            <div class="management-badges">
                <span class="management-badge">Georreferenciados: <?= (int) $mapPointCount ?></span>
                <span class="management-badge is-soft">Sem coordenadas: <?= (int) $withoutCoordinates ?></span>
                <span class="management-badge is-soft">Clique no ponto para abrir o detalhe</span>
            </div>
        </div>
    </section>

    <section class="management-metric-grid painel-metric-grid">
        <article class="management-metric-card">
            <span class="management-metric-label">Total de hidrantes</span>
            <strong class="management-metric-value"><?= (int) ($metrics['total'] ?? 0) ?></strong>
        </article>
        <article class="management-metric-card is-operador">
            <span class="management-metric-label">Operantes</span>
            <strong class="management-metric-value"><?= (int) ($metrics['operantes'] ?? 0) ?></strong>
        </article>
        <article class="management-metric-card is-gestor">
            <span class="management-metric-label">Com restrição</span>
            <strong class="management-metric-value"><?= (int) ($metrics['restricao'] ?? 0) ?></strong>
        </article>
        <article class="management-metric-card">
            <span class="management-metric-label">Inoperantes</span>
            <strong class="management-metric-value"><?= (int) ($metrics['inoperantes'] ?? 0) ?></strong>
        </article>
    </section>

    <section class="painel-layout">
        <article class="card management-card painel-map-card">
            <div class="management-section-head painel-map-head">
                <div>
                    <h3>Mapa operacional dos hidrantes</h3>
                    <p>Os pontos estão coloridos conforme o status operacional. Use as camadas do mapa, a rolagem do mouse para zoom e arraste com o mouse para navegar.</p>
                </div>
                <div class="painel-map-legend" aria-label="Legenda do mapa">
                    <span class="painel-legend-item">
                        <span class="painel-legend-dot is-operante"></span>
                        Operante
                    </span>
                    <span class="painel-legend-item">
                        <span class="painel-legend-dot is-restricao"></span>
                        Operante com restrição
                    </span>
                    <span class="painel-legend-item">
                        <span class="painel-legend-dot is-inoperante"></span>
                        Inoperante
                    </span>
                </div>
            </div>

            <div class="painel-map-controls">
                <div class="painel-map-toolbar" aria-label="Seletor de camadas do mapa">
                    <span class="painel-map-toolbar-label">Camadas</span>
                    <div class="painel-map-toolbar-actions painel-map-toolbar-actions-layers">
                        <button type="button" class="painel-map-layer-button is-active" data-painel-map-layer="streets" aria-pressed="true">Ruas</button>
                        <button type="button" class="painel-map-layer-button" data-painel-map-layer="operational" aria-pressed="false">Operacional</button>
                        <button type="button" class="painel-map-layer-button" data-painel-map-layer="satellite" aria-pressed="false">Satélite</button>
                    </div>
                </div>
                <div class="painel-map-toolbar painel-map-toolbar-secondary" aria-label="Ações de localização">
                    <span class="painel-map-toolbar-label">Minha localização</span>
                    <div class="painel-map-toolbar-actions painel-map-toolbar-actions-location">
                        <button type="button" class="painel-map-layer-button" id="painel-use-location-button">Usar minha localização</button>
                        <button type="button" class="painel-map-layer-button" id="painel-center-user-button" hidden>Centralizar no mapa</button>
                    </div>
                </div>
                <div class="painel-map-hint" id="painel-map-hint">
                    Use <strong>a rolagem do mouse</strong> para aproximar ou afastar. Arraste com o mouse para mover o mapa e use os seletores acima para trocar a camada.
                </div>
            </div>

            <div class="painel-map-shell">
                <div id="painel-map" class="painel-map-canvas" aria-label="Mapa com pontos dos hidrantes"></div>
                <div id="painel-map-empty" class="painel-map-empty" hidden>
                    <strong>Nenhum ponto georreferenciado disponível.</strong>
                    <p>Cadastre latitude e longitude nos hidrantes para visualizar a cobertura operacional no mapa do painel.</p>
                </div>
            </div>
        </article>

        <aside class="painel-side-stack">
            <article class="card management-card painel-side-card">
                <div class="management-section-head">
                    <h3>Leitura rápida do painel</h3>
                    <p>O painel foi organizado para consulta rápida em escritório ou em campo.</p>
                </div>
                <div class="painel-guide-list">
                    <div class="painel-guide-item">
                        <strong>1. Navegação no mapa</strong>
                        <p>Use a rolagem do mouse para zoom, arraste com o mouse para mover e troque a camada no seletor do mapa.</p>
                    </div>
                    <div class="painel-guide-item">
                        <strong>2. Minha localização</strong>
                        <p>Ative sua localização para marcar o seu ponto no mapa e listar os hidrantes mais próximos do seu dispositivo.</p>
                    </div>
                    <div class="painel-guide-item">
                        <strong>3. Abertura do detalhe</strong>
                        <p>Clique em qualquer ponto ou item da lista de proximidade para abrir o painel lateral com a ficha completa do hidrante.</p>
                    </div>
                    <div class="painel-guide-item">
                        <strong>4. Deslocamento rápido</strong>
                        <p>No detalhe, use o botão de rota para abrir o Google Maps e iniciar o deslocamento até o ponto.</p>
                    </div>
                </div>
            </article>

            <article class="card management-card painel-side-card">
                <div class="management-section-head">
                    <h3>Cobertura georreferenciada</h3>
                    <p>Indicadores de disponibilidade dos pontos no mapa operacional.</p>
                </div>
                <div class="painel-coverage-grid">
                    <div class="painel-coverage-item">
                        <span>Pontos no mapa</span>
                        <strong><?= (int) $mapPointCount ?></strong>
                    </div>
                    <div class="painel-coverage-item">
                        <span>Cadastros sem coordenadas</span>
                        <strong><?= (int) $withoutCoordinates ?></strong>
                    </div>
                </div>
            </article>

            <article class="card management-card painel-side-card">
                <div class="management-section-head">
                    <h3>Hidrantes mais próximos</h3>
                    <p>Use sua localização atual para identificar rapidamente os pontos mais próximos do hidrante em campo.</p>
                </div>
                <p class="painel-location-feedback" id="painel-location-feedback">
                    Ative sua localização para mostrar seu ponto no mapa e calcular os hidrantes mais próximos.
                </p>
                <div class="painel-nearest-empty" id="painel-nearest-empty">
                    Nenhuma localização capturada ainda.
                </div>
                <div class="painel-nearest-list" id="painel-nearest-list" hidden></div>
            </article>
        </aside>
    </section>
</div>

<script type="application/json" id="painel-map-points"><?= $mapPayloadJson === false ? '[]' : $mapPayloadJson ?></script>

<div class="painel-drawer-backdrop" id="painel-hidrante-drawer" hidden>
    <aside class="painel-drawer-card" role="dialog" aria-modal="true" aria-labelledby="painel-drawer-title">
        <div class="painel-drawer-header">
            <div>
                <p class="management-eyebrow">Detalhe do hidrante</p>
                <h2 id="painel-drawer-title">Hidrante</h2>
                <p class="painel-drawer-subtitle" id="painel-drawer-subtitle">Selecione um ponto no mapa.</p>
            </div>
            <button type="button" class="btn-secondary modal-close-button" data-painel-drawer-close>Fechar</button>
        </div>

        <div class="painel-drawer-body">
            <div class="painel-drawer-badges">
                <span class="painel-drawer-badge" id="painel-drawer-badge-number">-</span>
                <span class="painel-drawer-badge is-soft" id="painel-drawer-badge-region">-</span>
                <span class="painel-drawer-badge" id="painel-drawer-badge-status">-</span>
            </div>

            <div class="painel-drawer-actions">
                <a
                    class="btn-secondary"
                    id="painel-drawer-directions"
                    href="#"
                    target="_blank"
                    rel="noopener noreferrer"
                    hidden
                >
                    Abrir rota no Google Maps
                </a>
            </div>

            <section class="painel-drawer-section" id="painel-drawer-map-section" hidden>
                <div class="painel-drawer-section-head">
                    <h3>Localização no mapa</h3>
                    <span id="painel-drawer-coordinates">-</span>
                </div>
                <iframe
                    id="painel-drawer-map-frame"
                    class="painel-drawer-map-frame"
                    title="Mapa do hidrante selecionado"
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                ></iframe>
            </section>

            <section class="painel-drawer-section">
                <div class="painel-drawer-section-head">
                    <h3>Identificação e operação</h3>
                </div>
                <dl class="painel-drawer-detail-grid" id="painel-drawer-section-operacao"></dl>
            </section>

            <section class="painel-drawer-section">
                <div class="painel-drawer-section-head">
                    <h3>Condições físicas</h3>
                </div>
                <dl class="painel-drawer-detail-grid" id="painel-drawer-section-condicoes"></dl>
            </section>

            <section class="painel-drawer-section">
                <div class="painel-drawer-section-head">
                    <h3>Teste e desempenho</h3>
                </div>
                <dl class="painel-drawer-detail-grid" id="painel-drawer-section-teste"></dl>
            </section>

            <section class="painel-drawer-section">
                <div class="painel-drawer-section-head">
                    <h3>Localização e referência</h3>
                </div>
                <dl class="painel-drawer-detail-grid" id="painel-drawer-section-localizacao"></dl>
            </section>

            <section class="painel-drawer-section">
                <div class="painel-drawer-section-head">
                    <h3>Registro fotográfico</h3>
                    <span id="painel-drawer-photo-count">0 foto(s)</span>
                </div>
                <p class="painel-drawer-empty" id="painel-drawer-photo-empty">Nenhuma imagem cadastrada para este hidrante.</p>
                <div class="painel-drawer-photo-grid" id="painel-drawer-photo-grid" hidden></div>
            </section>
        </div>
    </aside>
</div>
