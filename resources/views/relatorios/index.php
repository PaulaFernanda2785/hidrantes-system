<?php
$items = $items ?? [];

function relatorio_status_class(?string $status): string
{
    return match (strtolower(trim((string) $status))) {
        'operante' => 'is-operante',
        'operante com restricao' => 'is-restricao',
        'inoperante' => 'is-inoperante',
        default => 'is-neutral',
    };
}
?>

<div class="management-page">
    <section class="card management-card management-hero">
        <div class="management-header">
            <div class="management-header-copy">
                <p class="management-eyebrow">Consolidacao operacional</p>
                <h1><?= e($title) ?></h1>
                <p class="management-description">
                    Gere um panorama rapido dos hidrantes por municipio e status operacional para apoiar analises e decisoes de acompanhamento.
                </p>
            </div>
            <div class="management-badges">
                <span class="management-badge">Registros: <?= count($items) ?></span>
                <span class="management-badge is-soft">
                    <?= !empty($filters['status_operacional']) ? 'Status: ' . e((string) $filters['status_operacional']) : 'Status: todos' ?>
                </span>
                <span class="management-badge is-soft">
                    <?= !empty($filters['municipio_id']) ? 'Municipio filtrado' : 'Todos os municipios' ?>
                </span>
            </div>
        </div>
    </section>

    <section class="card management-card">
        <div class="management-section-head">
            <h3>Filtros do relatorio</h3>
            <p>Selecione o status operacional e o municipio desejado para gerar a listagem consolidada.</p>
        </div>

        <form method="GET" action="/relatorios/hidrantes" class="filters-grid management-filters cols-2">
            <label>Status
                <select name="status_operacional">
                    <option value="">Todos</option>
                    <?php foreach (['operante', 'operante com restricao', 'inoperante'] as $status): ?>
                        <option value="<?= e($status) ?>" <?= ($filters['status_operacional'] ?? '') === $status ? 'selected' : '' ?>>
                            <?= e($status) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>Municipio
                <select name="municipio_id">
                    <option value="">Todos</option>
                    <?php foreach ($municipios as $municipio): ?>
                        <option value="<?= e((string) $municipio['id']) ?>" <?= (string) ($filters['municipio_id'] ?? '') === (string) $municipio['id'] ? 'selected' : '' ?>>
                            <?= e($municipio['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <div class="management-actions col-span-2">
                <button type="submit">Gerar relatorio</button>
                <a class="btn-secondary" href="/relatorios/hidrantes">Limpar</a>
            </div>
        </form>
    </section>

    <section class="card management-card">
        <div class="table-header management-results-header">
            <div>
                <strong>Total de registros:</strong> <?= count($items) ?>
            </div>
            <div>
                Resultado conforme os filtros selecionados
            </div>
        </div>

        <div class="management-table-shell">
            <table class="management-table">
                <thead>
                <tr>
                    <th>Numero</th>
                    <th>Municipio</th>
                    <th>Bairro</th>
                    <th>Status</th>
                    <th>Tipo</th>
                    <th>Area</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($items)): ?>
                    <tr>
                        <td colspan="6" class="management-empty">Nenhum hidrante encontrado para os filtros informados.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td data-label="Numero">
                                <div class="management-table-primary"><?= e($item['numero_hidrante'] ?? '-') ?></div>
                            </td>
                            <td data-label="Municipio"><?= e($item['municipio_nome'] ?? '-') ?></td>
                            <td data-label="Bairro"><?= e($item['bairro_nome'] ?? '-') ?></td>
                            <td data-label="Status">
                                <span class="management-status-badge <?= e(relatorio_status_class($item['status_operacional'] ?? '')) ?>">
                                    <?= e($item['status_operacional'] ?? '-') ?>
                                </span>
                            </td>
                            <td data-label="Tipo">
                                <span class="management-table-muted"><?= e($item['tipo_hidrante'] ?? '-') ?></span>
                            </td>
                            <td data-label="Area">
                                <span class="management-chip is-neutral"><?= e($item['area'] ?? '-') ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>
