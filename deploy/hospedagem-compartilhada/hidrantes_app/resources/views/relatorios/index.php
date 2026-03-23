<?php
require_once base_path('resources/views/relatorios/partials/helpers.php');

$items = $items ?? [];
$filters = $filters ?? [];
$municipios = $municipios ?? [];
$bairros = $bairros ?? [];
$document = $document ?? [];
$statusOperacional = (string) ($document['statusOperacional'] ?? trim((string) ($filters['status_operacional'] ?? '')));
$selectedMunicipioNome = (string) ($document['selectedMunicipioNome'] ?? relatorio_lookup_nome($municipios, $filters['municipio_id'] ?? null));
$selectedBairroNome = (string) ($document['selectedBairroNome'] ?? relatorio_lookup_nome($bairros, $filters['bairro_id'] ?? null));
$statusMetrics = $document['statusMetrics'] ?? [
    'total' => count($items),
    'operante' => 0,
    'operante com restricao' => 0,
    'inoperante' => 0,
];
$printPreviewUrl = (string) ($printPreviewUrl ?? '/relatorios/hidrantes/impressao');
?>

<div class="management-page">
    <section class="card management-card management-hero">
        <div class="management-header">
            <div class="management-header-copy">
                <p class="management-eyebrow">Emiss&atilde;o t&eacute;cnico-institucional</p>
                <h1><?= e($title) ?></h1>
                <p class="management-description">
                    Emita um relat&oacute;rio t&eacute;cnico-institucional dos hidrantes filtrados, com estrutura documental padronizada para an&aacute;lise operacional, registro administrativo e compartilhamento entre &oacute;rg&atilde;os competentes.
                </p>
            </div>
            <div class="management-badges">
                <span class="management-badge">Registros: <?= (int) $statusMetrics['total'] ?></span>
                <span class="management-badge is-soft">Munic&iacute;pio: <?= e($selectedMunicipioNome) ?></span>
                <span class="management-badge is-soft">Status: <?= e(relatorio_status_label($statusOperacional, 'Todos')) ?></span>
            </div>
        </div>
    </section>

    <section class="card management-card">
        <div class="management-section-head">
            <h3>Filtros do relat&oacute;rio</h3>
            <p>Aplique os filtros da base operacional para compor a listagem consolidada e gerar a vers&atilde;o documental pronta para impress&atilde;o.</p>
        </div>

        <form method="GET" action="/relatorios/hidrantes" class="filters-grid management-filters">
            <label>Busca
                <input
                    type="text"
                    name="q"
                    value="<?= e($filters['q'] ?? '') ?>"
                    placeholder="N&uacute;mero do hidrante, endere&ccedil;o ou equipe respons&aacute;vel"
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

            <label>Munic&iacute;pio
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
                <button type="submit">Gerar relat&oacute;rio t&eacute;cnico</button>
                <a class="btn-secondary" href="/relatorios/hidrantes">Limpar</a>
                <button type="button" class="btn-secondary" id="open-report-print-preview">Imprimir relat&oacute;rio</button>
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
            <span class="management-metric-label">Com restri&ccedil;&atilde;o</span>
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
                S&iacute;ntese operacional conforme os filtros aplicados
            </div>
        </div>

        <div class="management-table-shell">
            <table class="management-table">
                <thead>
                <tr>
                    <th>N&uacute;mero</th>
                    <th>Munic&iacute;pio</th>
                    <th>Bairro</th>
                    <th>Status</th>
                    <th>Tipo</th>
                    <th>&Aacute;rea</th>
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
                            <td data-label="N&uacute;mero">
                                <div class="management-table-primary"><?= e(relatorio_value($item['numero_hidrante'] ?? '')) ?></div>
                            </td>
                            <td data-label="Munic&iacute;pio"><?= e(relatorio_value($item['municipio_nome'] ?? '')) ?></td>
                            <td data-label="Bairro"><?= e(relatorio_value($item['bairro_nome'] ?? '')) ?></td>
                            <td data-label="Status">
                                <span class="management-status-badge <?= e(relatorio_status_class($item['status_operacional'] ?? '')) ?>">
                                    <?= e(relatorio_status_label($item['status_operacional'] ?? '')) ?>
                                </span>
                            </td>
                            <td data-label="Tipo"><?= e(relatorio_value($item['tipo_hidrante'] ?? '')) ?></td>
                            <td data-label="&Aacute;rea">
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
    <div class="modal-card report-preview-modal-card" role="dialog" aria-modal="true" aria-labelledby="report-print-title">
        <div class="modal-header report-preview-modal-header">
            <div>
                <p class="management-eyebrow">Documento institucional</p>
                <h2 id="report-print-title">Pr&eacute;-visualiza&ccedil;&atilde;o do relat&oacute;rio t&eacute;cnico</h2>
                <p class="modal-subtitle">A pr&eacute;-visualiza&ccedil;&atilde;o e a impress&atilde;o usam o mesmo documento, com a mesma composi&ccedil;&atilde;o visual.</p>
            </div>
            <div class="report-preview-modal-actions">
                <button type="button" id="trigger-report-print" disabled>Imprimir relat&oacute;rio</button>
                <button type="button" class="btn-secondary modal-close-button" data-report-print-close>Fechar</button>
            </div>
        </div>

        <div class="modal-body report-preview-modal-body">
            <p class="report-preview-status" id="report-preview-status">Carregando pr&eacute;-visualiza&ccedil;&atilde;o do relat&oacute;rio...</p>
            <iframe
                id="report-print-frame"
                class="report-preview-frame"
                src="<?= e($printPreviewUrl) ?>"
                title="Pr&eacute;-visualiza&ccedil;&atilde;o do relat&oacute;rio t&eacute;cnico de hidrantes"
                loading="lazy"
            ></iframe>
        </div>
    </div>
</div>
