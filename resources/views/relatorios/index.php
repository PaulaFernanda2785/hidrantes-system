<?php

use App\Core\Session;

$items = $items ?? [];
$filters = $filters ?? [];
$municipios = $municipios ?? [];
$bairros = $bairros ?? [];
$auth = Session::get('auth', []);

function relatorio_status_class(?string $status): string
{
    return match (strtolower(trim((string) $status))) {
        'operante' => 'is-operante',
        'operante com restricao' => 'is-restricao',
        'inoperante' => 'is-inoperante',
        default => 'is-neutral',
    };
}

function relatorio_value(mixed $value, string $fallback = 'Não informado'): string
{
    $normalized = trim((string) $value);

    return $normalized !== '' ? $normalized : $fallback;
}

function relatorio_status_label(?string $status, string $fallback = 'Todos'): string
{
    $normalized = trim((string) $status);

    if ($normalized === '') {
        return $fallback;
    }

    return match ($normalized) {
        'operante com restricao' => 'operante com restrição',
        default => $normalized,
    };
}

function relatorio_lookup_nome(array $items, string|int|null $id, string $fallback = 'Todos'): string
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

function relatorio_format_datetime(?string $value, string $fallback = 'Não informado'): string
{
    $normalized = trim((string) $value);

    if ($normalized === '') {
        return $fallback;
    }

    $timestamp = strtotime($normalized);

    if ($timestamp === false) {
        return $normalized;
    }

    return date('d/m/Y H:i', $timestamp);
}

function relatorio_photo_items(array $hidrante): array
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

$statusOperacional = trim((string) ($filters['status_operacional'] ?? ''));
$selectedMunicipioNome = relatorio_lookup_nome($municipios, $filters['municipio_id'] ?? null);
$selectedBairroNome = relatorio_lookup_nome($bairros, $filters['bairro_id'] ?? null);
$generatedAt = date('d/m/Y H:i');
$generatedBy = trim((string) ($auth['nome'] ?? '')) !== '' ? (string) $auth['nome'] : 'Sistema';
$generatedPerfil = trim((string) ($auth['perfil'] ?? '')) !== '' ? (string) $auth['perfil'] : 'técnico';

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
    'Busca' => relatorio_value($filters['q'] ?? '', 'Todos os registros'),
    'Status operacional' => relatorio_value($statusOperacional, 'Todos'),
    'Município' => $selectedMunicipioNome,
    'Bairro' => $selectedBairroNome,
    'Gerado em' => $generatedAt,
    'Responsável' => $generatedBy . ' (' . $generatedPerfil . ')',
];

$reportPages = [
    [
        'type' => 'cover',
    ],
    [
        'type' => 'summary',
    ],
];

foreach ($items as $item) {
    $photos = relatorio_photo_items($item);

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
?>

<div class="management-page">
    <section class="card management-card management-hero">
        <div class="management-header">
            <div class="management-header-copy">
                <p class="management-eyebrow">Consolidação institucional</p>
                <h1><?= e($title) ?></h1>
                <p class="management-description">
                    Gere um relatório técnico completo dos hidrantes filtrados, com estrutura pronta para impressão, análise operacional e encaminhamento a outros órgãos.
                </p>
            </div>
            <div class="management-badges">
                <span class="management-badge">Registros: <?= (int) $statusMetrics['total'] ?></span>
                <span class="management-badge is-soft">Município: <?= e($selectedMunicipioNome) ?></span>
                <span class="management-badge is-soft">Status: <?= e(relatorio_value($statusOperacional, 'todos')) ?></span>
            </div>
        </div>
    </section>

    <section class="card management-card">
        <div class="management-section-head">
            <h3>Filtros do relatório</h3>
            <p>Use os mesmos filtros da página de hidrantes para montar a listagem e gerar a versão institucional para impressão.</p>
        </div>

        <form method="GET" action="/relatorios/hidrantes" class="filters-grid management-filters">
            <label>Busca
                <input
                    type="text"
                    name="q"
                    value="<?= e($filters['q'] ?? '') ?>"
                    placeholder="Número, endereço ou equipe responsável"
                >
            </label>

            <label>Status
                <select name="status_operacional">
                    <option value="">Todos</option>
                    <?php foreach (['operante', 'operante com restricao', 'inoperante'] as $status): ?>
                        <option value="<?= e($status) ?>" <?= $statusOperacional === $status ? 'selected' : '' ?>>
                            <?= e(match ($status) {
                                'operante com restricao' => 'operante com restrição',
                                default => $status,
                            }) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>Município
                <select name="municipio_id" id="filter-municipio-id">
                    <option value="">Todos</option>
                    <?php foreach ($municipios as $municipio): ?>
                        <option value="<?= e((string) $municipio['id']) ?>" <?= (string) ($filters['municipio_id'] ?? '') === (string) $municipio['id'] ? 'selected' : '' ?>>
                            <?= e($municipio['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>Bairro
                <select
                    name="bairro_id"
                    id="filter-bairro-id"
                    <?= empty($filters['municipio_id']) ? 'disabled' : '' ?>
                    data-selected-bairro-id="<?= e((string) ($filters['bairro_id'] ?? '')) ?>"
                >
                    <option value="">Todos</option>
                    <?php foreach ($bairros as $bairro): ?>
                        <option value="<?= e((string) $bairro['id']) ?>" <?= (string) ($filters['bairro_id'] ?? '') === (string) $bairro['id'] ? 'selected' : '' ?>>
                            <?= e($bairro['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <div class="management-actions col-span-2">
                <button type="submit">Gerar relatório</button>
                <a class="btn-secondary" href="/relatorios/hidrantes">Limpar</a>
                <button type="button" class="btn-secondary" id="open-report-print-preview">Imprimir relatório</button>
            </div>
        </form>
    </section>

    <section class="management-metric-grid">
        <article class="management-metric-card">
            <span class="management-metric-label">Total filtrado</span>
            <strong class="management-metric-value"><?= (int) $statusMetrics['total'] ?></strong>
        </article>
        <article class="management-metric-card is-operador">
            <span class="management-metric-label">Operantes</span>
            <strong class="management-metric-value"><?= (int) $statusMetrics['operante'] ?></strong>
        </article>
        <article class="management-metric-card is-gestor">
            <span class="management-metric-label">Com restrição</span>
            <strong class="management-metric-value"><?= (int) $statusMetrics['operante com restricao'] ?></strong>
        </article>
        <article class="management-metric-card">
            <span class="management-metric-label">Inoperantes</span>
            <strong class="management-metric-value"><?= (int) $statusMetrics['inoperante'] ?></strong>
        </article>
    </section>

    <section class="card management-card">
        <div class="table-header management-results-header">
            <div>
                <strong>Total de registros:</strong> <?= (int) $statusMetrics['total'] ?>
            </div>
            <div>
                Visualização sintética conforme os filtros aplicados
            </div>
        </div>

        <div class="management-table-shell">
            <table class="management-table">
                <thead>
                <tr>
                    <th>Número</th>
                    <th>Município</th>
                    <th>Bairro</th>
                    <th>Status</th>
                    <th>Tipo</th>
                    <th>Área</th>
                    <th>Atualizado em</th>
                </tr>
                </thead>
                <tbody>
                <?php if ($items === []): ?>
                    <tr>
                        <td colspan="7" class="management-empty">Nenhum hidrante encontrado para os filtros informados.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td data-label="Número">
                                <div class="management-table-primary"><?= e(relatorio_value($item['numero_hidrante'] ?? '')) ?></div>
                            </td>
                            <td data-label="Município"><?= e(relatorio_value($item['municipio_nome'] ?? '')) ?></td>
                            <td data-label="Bairro"><?= e(relatorio_value($item['bairro_nome'] ?? '')) ?></td>
                            <td data-label="Status">
                                <span class="management-status-badge <?= e(relatorio_status_class($item['status_operacional'] ?? '')) ?>">
                                    <?= e(match (trim((string) ($item['status_operacional'] ?? ''))) {
                                        'operante com restricao' => 'operante com restrição',
                                        default => relatorio_value($item['status_operacional'] ?? ''),
                                    }) ?>
                                </span>
                            </td>
                            <td data-label="Tipo"><?= e(relatorio_value($item['tipo_hidrante'] ?? '')) ?></td>
                            <td data-label="Área">
                                <span class="management-chip is-neutral"><?= e(relatorio_value($item['area'] ?? '')) ?></span>
                            </td>
                            <td data-label="Atualizado em"><?= e(relatorio_format_datetime($item['atualizado_em'] ?? '')) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

<div class="modal-backdrop" id="report-print-modal" hidden>
    <div class="modal-card report-print-modal-card" role="dialog" aria-modal="true" aria-labelledby="report-print-title">
        <div class="modal-header">
            <div>
                <h2 id="report-print-title">Pré-visualização do relatório institucional</h2>
                <p class="modal-subtitle">Documento consolidado com capa, filtros aplicados, sumário e fichas técnicas dos hidrantes selecionados.</p>
            </div>
            <button type="button" class="btn-secondary modal-close-button" data-report-print-close>Fechar</button>
        </div>

        <div class="modal-body report-print-modal-body">
            <div class="management-actions report-print-toolbar">
                <div>
                    <strong>Pronto para impressão</strong>
                    <p class="management-table-muted">A versão abaixo repete cabeçalho e rodapé em todas as páginas e organiza os hidrantes sem cortes indevidos.</p>
                </div>
                <div class="management-actions report-print-toolbar-actions">
                    <button type="button" id="trigger-report-print">Imprimir</button>
                </div>
            </div>

            <div class="report-print-surface">
                <div class="report-preview-pages">
                    <?php foreach ($reportPages as $page): ?>
                        <?php
                        $pageType = (string) ($page['type'] ?? 'summary');
                        $pageItem = $page['item'] ?? null;
                        $pagePhotos = $page['photos'] ?? [];
                        ?>
                        <article class="report-page report-page--<?= e($pageType) ?>">
                            <header class="report-page-header">
                                <div class="report-page-brand">
                                    <img src="/img/logos/logo.cbmpa.png" alt="CBMPA" class="report-page-brand-logo">
                                    <div class="report-page-brand-copy">
                                        <strong class="report-page-brand-title">Corpo de Bombeiros Militar do Estado do Pará</strong>
                                        <strong class="report-page-brand-title">Coordenadoria Estadual de Proteção e Defesa Civil</strong>
                                        <small>Sistema de Gestão de Hidrantes | Relatório técnico institucional</small>
                                    </div>
                                </div>
                                <div class="report-page-meta">
                                    <span>Gerado em: <?= e($generatedAt) ?></span>
                                    <span>Responsável: <?= e($generatedBy) ?></span>
                                </div>
                            </header>

                            <div class="report-page-body">
                                <?php if ($pageType === 'cover'): ?>
                                    <section class="report-cover-panel">
                                        <p class="management-eyebrow">Documento institucional</p>
                                        <h2>Relatório técnico de hidrantes cadastrados</h2>
                                        <p class="report-cover-lead">
                                            Material consolidado para análise, acompanhamento operacional e encaminhamento técnico a outros órgãos do Estado.
                                        </p>

                                        <div class="report-page-grid report-page-grid--two">
                                            <div class="report-page-section">
                                                <h3>Escopo do documento</h3>
                                                <p>
                                                    O relatório apresenta os hidrantes filtrados no sistema, com dados de identificação, condições físicas, resultado de teste, localização e registro fotográfico.
                                                </p>
                                            </div>
                                            <div class="report-page-section">
                                                <h3>Panorama rápido</h3>
                                                <div class="report-summary-grid">
                                                    <article class="management-metric-card">
                                                        <span class="management-metric-label">Total</span>
                                                        <strong class="management-metric-value"><?= (int) $statusMetrics['total'] ?></strong>
                                                    </article>
                                                    <article class="management-metric-card is-operador">
                                                        <span class="management-metric-label">Operantes</span>
                                                        <strong class="management-metric-value"><?= (int) $statusMetrics['operante'] ?></strong>
                                                    </article>
                                                    <article class="management-metric-card is-gestor">
                                                        <span class="management-metric-label">Restrição</span>
                                                        <strong class="management-metric-value"><?= (int) $statusMetrics['operante com restricao'] ?></strong>
                                                    </article>
                                                    <article class="management-metric-card">
                                                        <span class="management-metric-label">Inoperantes</span>
                                                        <strong class="management-metric-value"><?= (int) $statusMetrics['inoperante'] ?></strong>
                                                    </article>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="report-cover-summary">
                                            <strong>Referência institucional</strong>
                                            <p>Documento gerado no Sistema de Gestão de Hidrantes para subsidiar avaliações técnicas, operacionais e administrativas.</p>
                                        </div>
                                    </section>
                                <?php elseif ($pageType === 'summary'): ?>
                                    <section class="report-page-section">
                                        <div class="report-sheet-header">
                                            <div>
                                                <h3>Parâmetros do filtro</h3>
                                                <p class="management-table-muted">Resumo do conjunto utilizado para gerar este relatório.</p>
                                            </div>
                                            <span class="management-chip is-relatorios">Documento consolidado</span>
                                        </div>
                                        <dl class="report-filter-grid">
                                            <?php foreach ($filterSummary as $label => $value): ?>
                                                <div class="report-filter-item">
                                                    <dt><?= e($label) ?></dt>
                                                    <dd><?= e($value) ?></dd>
                                                </div>
                                            <?php endforeach; ?>
                                        </dl>
                                    </section>

                                    <section class="report-page-grid report-page-grid--two">
                                        <div class="report-page-section">
                                            <h3>Sumário das seções</h3>
                                            <div class="report-section-outline">
                                                <div class="report-outline-item">
                                                    <strong>1. Identificação e operação</strong>
                                                    <span>Número do hidrante, equipe, área, tipo e status operacional.</span>
                                                </div>
                                                <div class="report-outline-item">
                                                    <strong>2. Condições físicas</strong>
                                                    <span>Acesso, caixa, tampas, conexões e presença de água.</span>
                                                </div>
                                                <div class="report-outline-item">
                                                    <strong>3. Teste e desempenho</strong>
                                                    <span>Execução do teste, resultado obtido e leitura operacional.</span>
                                                </div>
                                                <div class="report-outline-item">
                                                    <strong>4. Localização e referência</strong>
                                                    <span>Município, bairro, endereço e coordenadas do ponto.</span>
                                                </div>
                                                <div class="report-outline-item">
                                                    <strong>5. Registro fotográfico</strong>
                                                    <span>Fotos organizadas em página própria quando disponíveis.</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="report-page-section">
                                            <h3>Observação técnica</h3>
                                            <p class="report-empty-note">
                                                A ficha individual de cada hidrante foi compactada para caber de forma consistente na impressão, preservando a leitura e evitando cortes no meio do registro.
                                            </p>
                                            <p class="report-empty-note">
                                                Sempre que houver imagens cadastradas, o relatório inclui uma página fotográfica dedicada para manter a qualidade visual do documento.
                                            </p>
                                        </div>
                                    </section>
                                <?php elseif ($pageType === 'hidrante' && is_array($pageItem)): ?>
                                    <section class="report-page-section report-page-section--compact">
                                        <div class="report-entry-header">
                                            <div class="report-hydrant-heading">
                                                <p class="management-eyebrow">Ficha técnica</p>
                                                <h3>Hidrante <?= e(relatorio_value($pageItem['numero_hidrante'] ?? '')) ?></h3>
                                                <p class="management-table-muted">
                                                    <?= e(relatorio_value($pageItem['municipio_nome'] ?? '')) ?><?php if (trim((string) ($pageItem['bairro_nome'] ?? '')) !== ''): ?> | <?= e($pageItem['bairro_nome']) ?><?php endif; ?>
                                                </p>
                                            </div>
                                            <div class="report-entry-meta">
                                                <span class="management-status-badge <?= e(relatorio_status_class($pageItem['status_operacional'] ?? '')) ?>">
                                                    <?= e(relatorio_value($pageItem['status_operacional'] ?? '')) ?>
                                                </span>
                                                <span class="management-chip is-neutral">Atualizado em <?= e(relatorio_format_datetime($pageItem['atualizado_em'] ?? '')) ?></span>
                                            </div>
                                        </div>
                                    </section>

                                    <section class="report-page-grid report-page-grid--two">
                                        <section class="report-page-section report-page-section--compact">
                                            <h3>Identificação e operação</h3>
                                            <dl class="report-detail-grid report-detail-grid--compact">
                                                <div class="report-detail-item">
                                                    <dt>Número</dt>
                                                    <dd><?= e(relatorio_value($pageItem['numero_hidrante'] ?? '')) ?></dd>
                                                </div>
                                                <div class="report-detail-item">
                                                    <dt>Equipe responsável</dt>
                                                    <dd><?= e(relatorio_value($pageItem['equipe_responsavel'] ?? '')) ?></dd>
                                                </div>
                                                <div class="report-detail-item">
                                                    <dt>Área</dt>
                                                    <dd><?= e(relatorio_value($pageItem['area'] ?? '')) ?></dd>
                                                </div>
                                                <div class="report-detail-item">
                                                    <dt>Existe no local</dt>
                                                    <dd><?= e(relatorio_value($pageItem['existe_no_local'] ?? '')) ?></dd>
                                                </div>
                                                <div class="report-detail-item detail-item-full">
                                                    <dt>Tipo do hidrante</dt>
                                                    <dd><?= e(relatorio_value($pageItem['tipo_hidrante'] ?? '')) ?></dd>
                                                </div>
                                            </dl>
                                        </section>

                                        <section class="report-page-section report-page-section--compact">
                                            <h3>Condições físicas</h3>
                                            <dl class="report-detail-grid report-detail-grid--compact">
                                                <div class="report-detail-item">
                                                    <dt>Acessibilidade</dt>
                                                    <dd><?= e(relatorio_value($pageItem['acessibilidade'] ?? '')) ?></dd>
                                                </div>
                                                <div class="report-detail-item">
                                                    <dt>Tampo e conexões</dt>
                                                    <dd><?= e(relatorio_value($pageItem['tampo_conexoes'] ?? '')) ?></dd>
                                                </div>
                                                <div class="report-detail-item">
                                                    <dt>Tampas ausentes</dt>
                                                    <dd><?= e(relatorio_value($pageItem['tampas_ausentes'] ?? '')) ?></dd>
                                                </div>
                                                <div class="report-detail-item">
                                                    <dt>Caixa de proteção</dt>
                                                    <dd><?= e(relatorio_value($pageItem['caixa_protecao'] ?? '')) ?></dd>
                                                </div>
                                                <div class="report-detail-item">
                                                    <dt>Condição da caixa</dt>
                                                    <dd><?= e(relatorio_value($pageItem['condicao_caixa'] ?? '')) ?></dd>
                                                </div>
                                                <div class="report-detail-item">
                                                    <dt>Presença de água</dt>
                                                    <dd><?= e(relatorio_value($pageItem['presenca_agua_interior'] ?? '')) ?></dd>
                                                </div>
                                            </dl>
                                        </section>

                                        <section class="report-page-section report-page-section--compact">
                                            <h3>Teste e desempenho</h3>
                                            <dl class="report-detail-grid report-detail-grid--compact">
                                                <div class="report-detail-item">
                                                    <dt>Teste realizado</dt>
                                                    <dd><?= e(relatorio_value($pageItem['teste_realizado'] ?? '')) ?></dd>
                                                </div>
                                                <div class="report-detail-item detail-item-full">
                                                    <dt>Resultado do teste</dt>
                                                    <dd><?= e(relatorio_value($pageItem['resultado_teste'] ?? '')) ?></dd>
                                                </div>
                                                <div class="report-detail-item detail-item-full">
                                                    <dt>Status operacional</dt>
                                                    <dd><?= e(relatorio_value($pageItem['status_operacional'] ?? '')) ?></dd>
                                                </div>
                                            </dl>
                                        </section>

                                        <section class="report-page-section report-page-section--compact">
                                            <h3>Localização e referência</h3>
                                            <dl class="report-detail-grid report-detail-grid--compact">
                                                <div class="report-detail-item">
                                                    <dt>Município</dt>
                                                    <dd><?= e(relatorio_value($pageItem['municipio_nome'] ?? '')) ?></dd>
                                                </div>
                                                <div class="report-detail-item">
                                                    <dt>Bairro</dt>
                                                    <dd><?= e(relatorio_value($pageItem['bairro_nome'] ?? '')) ?></dd>
                                                </div>
                                                <div class="report-detail-item detail-item-full">
                                                    <dt>Endereço</dt>
                                                    <dd><?= e(relatorio_value($pageItem['endereco'] ?? '')) ?></dd>
                                                </div>
                                                <div class="report-detail-item">
                                                    <dt>Latitude</dt>
                                                    <dd><?= e(relatorio_value($pageItem['latitude'] ?? '')) ?></dd>
                                                </div>
                                                <div class="report-detail-item">
                                                    <dt>Longitude</dt>
                                                    <dd><?= e(relatorio_value($pageItem['longitude'] ?? '')) ?></dd>
                                                </div>
                                                <div class="report-detail-item">
                                                    <dt>Criado em</dt>
                                                    <dd><?= e(relatorio_format_datetime($pageItem['criado_em'] ?? '')) ?></dd>
                                                </div>
                                                <div class="report-detail-item">
                                                    <dt>Atualizado em</dt>
                                                    <dd><?= e(relatorio_format_datetime($pageItem['atualizado_em'] ?? '')) ?></dd>
                                                </div>
                                            </dl>
                                        </section>
                                    </section>
                                <?php elseif ($pageType === 'photos' && is_array($pageItem)): ?>
                                    <section class="report-page-section report-page-section--compact">
                                        <div class="report-entry-header">
                                            <div class="report-hydrant-heading">
                                                <p class="management-eyebrow">Registro fotográfico</p>
                                                <h3>Hidrante <?= e(relatorio_value($pageItem['numero_hidrante'] ?? '')) ?></h3>
                                                <p class="management-table-muted">
                                                    <?= e(relatorio_value($pageItem['municipio_nome'] ?? '')) ?><?php if (trim((string) ($pageItem['bairro_nome'] ?? '')) !== ''): ?> | <?= e($pageItem['bairro_nome']) ?><?php endif; ?>
                                                </p>
                                            </div>
                                            <span class="management-chip is-neutral">Fotos anexadas: <?= count($pagePhotos) ?></span>
                                        </div>
                                    </section>

                                    <?php if ($pagePhotos === []): ?>
                                        <section class="report-page-section report-page-section--compact">
                                            <p class="report-empty-note">Nenhuma fotografia foi cadastrada para este hidrante.</p>
                                        </section>
                                    <?php else: ?>
                                        <section class="report-photo-page-grid">
                                            <?php foreach ($pagePhotos as $photo): ?>
                                                <figure class="report-photo-card report-photo-card--compact">
                                                    <img src="<?= e($photo['url']) ?>" alt="<?= e($photo['label']) ?>" class="report-photo-image">
                                                    <figcaption><?= e($photo['label']) ?></figcaption>
                                                </figure>
                                            <?php endforeach; ?>
                                        </section>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>

                            <footer class="report-page-footer">
                                <div class="report-page-footer-copy">
                                    <strong>Documento técnico institucional</strong>
                                    <span>Uso destinado a análise, acompanhamento e compartilhamento entre órgãos competentes.</span>
                                </div>
                                <div class="report-page-footer-page">Página <?= (int) $page['page_number'] ?> de <?= (int) $page['total_pages'] ?></div>
                            </footer>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
