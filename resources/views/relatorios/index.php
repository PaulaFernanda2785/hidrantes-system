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
    $normalized = preg_replace('/\s+/u', ' ', trim((string) $value));
    $normalized = is_string($normalized) ? trim($normalized) : '';

    return $normalized !== '' ? $normalized : $fallback;
}

function relatorio_status_label(?string $status, string $fallback = 'Todos'): string
{
    $normalized = strtolower(trim((string) $status));

    if ($normalized === '') {
        return $fallback;
    }

    return match ($normalized) {
        'operante' => 'Operante',
        'operante com restricao' => 'Operante com restrição',
        'inoperante' => 'Inoperante',
        default => relatorio_value($status, $fallback),
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
$generatedBy = relatorio_value($auth['nome'] ?? '', 'Sistema');

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
    'Status' => relatorio_status_label($statusOperacional, 'Todos'),
    'Município' => $selectedMunicipioNome,
    'Bairro' => $selectedBairroNome,
    'Emissão' => $generatedAt,
    'Responsável' => $generatedBy,
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
                <p class="management-eyebrow">Emissão técnico-institucional</p>
                <h1><?= e($title) ?></h1>
                <p class="management-description">
                    Emita um relatório técnico-institucional dos hidrantes filtrados, com estrutura documental padronizada para análise operacional, registro administrativo e compartilhamento entre órgãos competentes.
                </p>
            </div>
            <div class="management-badges">
                <span class="management-badge">Registros: <?= (int) $statusMetrics['total'] ?></span>
                <span class="management-badge is-soft">Município: <?= e($selectedMunicipioNome) ?></span>
                <span class="management-badge is-soft">Status: <?= e(relatorio_status_label($statusOperacional, 'Todos')) ?></span>
            </div>
        </div>
    </section>

    <section class="card management-card">
        <div class="management-section-head">
            <h3>Filtros do relatório</h3>
            <p>Aplique os filtros da base operacional para compor a listagem consolidada e gerar a versão documental pronta para impressão.</p>
        </div>

        <form method="GET" action="/relatorios/hidrantes" class="filters-grid management-filters">
            <label>Busca
                <input
                    type="text"
                    name="q"
                    value="<?= e($filters['q'] ?? '') ?>"
                    placeholder="Número do hidrante, endereço ou equipe responsável"
                >
            </label>

            <label>Status
                <select name="status_operacional">
                    <option value="">Todos</option>
                    <?php foreach (['operante', 'operante com restricao', 'inoperante'] as $status): ?>
                        <option value="<?= e($status) ?>" <?= $statusOperacional === $status ? 'selected' : '' ?>>
                            <?= e(relatorio_status_label($status, 'Todos')) ?>
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
                <button type="submit">Gerar relatório técnico</button>
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
                Síntese operacional conforme os filtros aplicados
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
                                    <?= e(relatorio_status_label($item['status_operacional'] ?? '', 'Não informado')) ?>
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
        <div class="modal-header report-print-modal-header">
            <div>
                <p class="management-eyebrow">Documento institucional</p>
                <h2 id="report-print-title">Pré-visualização do relatório técnico</h2>
                <p class="modal-subtitle">Documento com capa institucional, parâmetros de emissão e fichas técnicas dos hidrantes selecionados.</p>
            </div>
            <div class="report-print-modal-actions">
                <button type="button" id="trigger-report-print">Imprimir relatório</button>
                <button type="button" class="btn-secondary modal-close-button" data-report-print-close>Fechar</button>
            </div>
        </div>

        <div class="modal-body report-print-modal-body">
            <div class="report-print-context-card">
                <div class="report-print-context-copy">
                    <strong>Pré-visualização no padrão do manual do usuário</strong>
                    <p class="management-table-muted">O relatório segue o mesmo padrão técnico do manual, com capa, cabeçalho, rodapé e paginação institucional.</p>
                </div>
                <dl class="report-print-context-grid">
                    <div class="report-print-context-item">
                        <dt>Código</dt>
                        <dd>RT-SGH-001</dd>
                    </div>
                    <div class="report-print-context-item">
                        <dt>Versão</dt>
                        <dd>1.0.0</dd>
                    </div>
                    <div class="report-print-context-item">
                        <dt>Responsável</dt>
                        <dd><?= e($generatedBy) ?></dd>
                    </div>
                    <div class="report-print-context-item">
                        <dt>Páginas</dt>
                        <dd><?= (int) $totalReportPages ?></dd>
                    </div>
                </dl>
            </div>

            <div class="report-print-surface">
                <div class="report-preview-pages">
                    <?php foreach ($reportPages as $page): ?>
                        <?php
                        $pageType = (string) ($page['type'] ?? 'summary');
                        $pageItem = $page['item'] ?? null;
                        $pagePhotos = $page['photos'] ?? [];
                        $pageHeaderSubtitle = match ($pageType) {
                            'cover' => 'Capa institucional e consolidação técnica.',
                            'summary' => 'Parâmetros da emissão e sumário executivo.',
                            'hidrante' => 'Ficha técnica individual.',
                            'photos' => 'Registro fotográfico do hidrante.',
                            default => 'Documento institucional para impressão.',
                        };
                        ?>
                        <article
                            class="report-page report-page--<?= e($pageType) ?>"
                            data-report-page="<?= (int) ($page['page_number'] ?? 0) ?>"
                            data-report-type="<?= e($pageType) ?>"
                        >
                            <header class="report-page-header">
                                <div class="report-page-header-copy">
                                    <p class="management-eyebrow">Documento institucional</p>
                                    <h2 class="report-page-title">Relatório técnico de hidrantes</h2>
                                    <p class="modal-subtitle"><?= e($pageHeaderSubtitle) ?></p>
                                </div>
                            </header>

                            <div class="report-page-body">
                                <?php if ($pageType === 'cover'): ?>
                                    <section class="report-cover-panel">
                                        <span class="report-cover-mark">Documento institucional</span>
                                        <div class="report-cover-seal">
                                            <img src="/img/logos/logo.cbmpa.png" alt="CBMPA" class="report-cover-seal-logo">
                                            <div class="report-cover-seal-copy">
                                                <strong>Relatório técnico institucional</strong>
                                                <span>Modelo para impressão e protocolo</span>
                                            </div>
                                        </div>
                                        <h2>Relatório técnico de hidrantes</h2>
                                        <p class="report-cover-lead">
                                            Documento institucional para análise operacional e encaminhamento administrativo.
                                        </p>

                                        <div class="report-cover-document-grid">
                                            <div class="report-cover-document-item">
                                                <span>Código documental</span>
                                                <strong>RT-SGH-001</strong>
                                            </div>
                                            <div class="report-cover-document-item">
                                                <span>Versão</span>
                                                <strong>1.0.0</strong>
                                            </div>
                                            <div class="report-cover-document-item">
                                                <span>Gerado em</span>
                                                <strong><?= e($generatedAt) ?></strong>
                                            </div>
                                            <div class="report-cover-document-item">
                                                <span>Responsável</span>
                                                <strong><?= e($generatedBy) ?></strong>
                                            </div>
                                        </div>

                                        <div class="report-page-grid report-page-grid--two">
                                            <div class="report-page-section">
                                                <h3>Escopo do documento</h3>
                                                <p>
                                                    O relatório reúne os hidrantes filtrados, com identificação, condição física, teste, localização e registro fotográfico em formato padronizado.
                                                </p>
                                            </div>
                                            <div class="report-page-section">
                                                <h3>Painel executivo</h3>
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
                                            <p>Documento emitido pelo Sistema de Gestão de Hidrantes para apoio técnico do CBMPA / CEDEC-PA.</p>
                                        </div>
                                    </section>
                                <?php elseif ($pageType === 'summary'): ?>
                                    <section class="report-page-section report-page-section--formal">
                                        <div class="report-sheet-header">
                                            <div>
                                                <h3>Parâmetros da emissão</h3>
                                                <p class="management-table-muted">Critérios aplicados na emissão deste relatório.</p>
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
                                        <div class="report-page-section report-page-section--formal">
                                            <h3>Sumário das seções</h3>
                                            <div class="report-section-outline">
                                                <div class="report-outline-item">
                                                    <strong>1. Identificação e operação</strong>
                                                    <span>Número, equipe, área, tipo e status operacional.</span>
                                                </div>
                                                <div class="report-outline-item">
                                                    <strong>2. Condições físicas</strong>
                                                    <span>Acesso, caixa, tampas, conexões e água.</span>
                                                </div>
                                                <div class="report-outline-item">
                                                    <strong>3. Teste e desempenho</strong>
                                                    <span>Execução do teste, resultado e leitura operacional.</span>
                                                </div>
                                                <div class="report-outline-item">
                                                    <strong>4. Localização e referência</strong>
                                                    <span>Município, bairro, endereço e coordenadas.</span>
                                                </div>
                                                <div class="report-outline-item">
                                                    <strong>5. Registro fotográfico</strong>
                                                    <span>Fotos em página própria, quando disponíveis.</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="report-page-section report-page-section--formal">
                                            <h3>Observação técnica</h3>
                                            <p class="report-empty-note">
                                                O layout foi ajustado para impressão em A4, com leitura técnica estável e menor risco de cortes indevidos.
                                            </p>
                                            <p class="report-empty-note">
                                                Quando houver imagens, o relatório inclui página fotográfica dedicada para preservar leitura e rastreabilidade.
                                            </p>
                                        </div>
                                    </section>
                                <?php elseif ($pageType === 'hidrante' && is_array($pageItem)): ?>
                                    <section class="report-page-section report-page-section--compact report-page-section--formal">
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
                                                    <?= e(relatorio_status_label($pageItem['status_operacional'] ?? '', 'Não informado')) ?>
                                                </span>
                                                <span class="management-chip is-neutral">Atualizado em <?= e(relatorio_format_datetime($pageItem['atualizado_em'] ?? '')) ?></span>
                                            </div>
                                        </div>
                                    </section>

                                    <section class="report-page-grid report-page-grid--two">
                                        <section class="report-page-section report-page-section--compact report-page-section--formal">
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

                                        <section class="report-page-section report-page-section--compact report-page-section--formal">
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

                                        <section class="report-page-section report-page-section--compact report-page-section--formal">
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
                                                    <dd><?= e(relatorio_status_label($pageItem['status_operacional'] ?? '', 'Não informado')) ?></dd>
                                                </div>
                                            </dl>
                                        </section>

                                        <section class="report-page-section report-page-section--compact report-page-section--formal">
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
                                    <section class="report-page-section report-page-section--compact report-page-section--formal">
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
                                        <section class="report-page-section report-page-section--compact report-page-section--formal">
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
                                    <span>CBMPA / CEDEC-PA | Sistema de Gestão de Hidrantes</span>
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
