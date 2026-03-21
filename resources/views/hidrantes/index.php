<?php

use App\Core\Session;

$auth = Session::get('auth');
$perfil = $auth['perfil'] ?? '';

$pagination = $pagination ?? [
    'current_page' => 1,
    'per_page' => 15,
    'total' => 0,
    'last_page' => 1,
    'from' => 0,
    'to' => 0,
];

function build_page_url(int $page, array $filters): string
{
    $query = array_filter([
        'page' => $page,
        'q' => $filters['q'] ?? '',
        'status_operacional' => $filters['status_operacional'] ?? '',
        'municipio_id' => $filters['municipio_id'] ?? '',
        'bairro_id' => $filters['bairro_id'] ?? '',
    ], static fn($value) => $value !== null && $value !== '');

    return '/hidrantes?' . http_build_query($query);
}

function build_csv_url(array $filters): string
{
    $query = array_filter([
        'q' => $filters['q'] ?? '',
        'status_operacional' => $filters['status_operacional'] ?? '',
        'municipio_id' => $filters['municipio_id'] ?? '',
        'bairro_id' => $filters['bairro_id'] ?? '',
    ], static fn($value) => $value !== null && $value !== '');

    $suffix = $query === [] ? '' : '?' . http_build_query($query);

    return '/hidrantes/exportar/csv' . $suffix;
}

function hidrante_photo_items(array $hidrante): array
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

function hidrante_detail_payload(array $hidrante): string
{
    $payload = [
        'numero_hidrante' => (string) ($hidrante['numero_hidrante'] ?? '-'),
        'equipe_responsavel' => (string) ($hidrante['equipe_responsavel'] ?? '-'),
        'area' => (string) ($hidrante['area'] ?? '-'),
        'existe_no_local' => (string) ($hidrante['existe_no_local'] ?? '-'),
        'tipo_hidrante' => (string) ($hidrante['tipo_hidrante'] ?? '-'),
        'acessibilidade' => (string) ($hidrante['acessibilidade'] ?? '-'),
        'tampo_conexoes' => (string) ($hidrante['tampo_conexoes'] ?? '-'),
        'tampas_ausentes' => (string) ($hidrante['tampas_ausentes'] ?? '-'),
        'caixa_protecao' => (string) ($hidrante['caixa_protecao'] ?? '-'),
        'condicao_caixa' => (string) ($hidrante['condicao_caixa'] ?? '-'),
        'presenca_agua_interior' => (string) ($hidrante['presenca_agua_interior'] ?? '-'),
        'teste_realizado' => (string) ($hidrante['teste_realizado'] ?? '-'),
        'resultado_teste' => (string) ($hidrante['resultado_teste'] ?? '-'),
        'status_operacional' => (string) ($hidrante['status_operacional'] ?? '-'),
        'municipio_nome' => (string) ($hidrante['municipio_nome'] ?? '-'),
        'bairro_nome' => (string) (($hidrante['bairro_nome'] ?? '') !== '' ? $hidrante['bairro_nome'] : '-'),
        'endereco' => (string) ($hidrante['endereco'] ?? '-'),
        'latitude' => (string) ($hidrante['latitude'] ?? ''),
        'longitude' => (string) ($hidrante['longitude'] ?? ''),
        'criado_em' => (string) ($hidrante['criado_em'] ?? '-'),
        'atualizado_em' => (string) ($hidrante['atualizado_em'] ?? '-'),
        'fotos' => hidrante_photo_items($hidrante),
    ];

    $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    return $json === false ? '{}' : e($json);
}

function hidrante_status_class(?string $status): string
{
    return match (strtolower(trim((string) $status))) {
        'operante' => 'is-operante',
        'operante com restricao' => 'is-restricao',
        'inoperante' => 'is-inoperante',
        default => 'is-neutral',
    };
}
?>

<div class="hidrante-listing-page">
    <section class="card hidrante-listing-card hidrante-listing-hero">
        <div class="hidrante-form-header">
            <div class="hidrante-form-header-copy">
                <p class="hidrante-form-eyebrow">Gestao operacional</p>
                <h1><?= e($title) ?></h1>
                <p class="hidrante-form-description">
                    Consulte rapidamente os hidrantes cadastrados, aplique filtros por municipio e status e acompanhe o ultimo registro atualizado em campo.
                </p>
            </div>
            <div class="hidrante-form-header-badges">
                <span class="hidrante-form-badge">Total: <?= (int) $pagination['total'] ?></span>
                <span class="hidrante-form-badge is-soft">Exibindo <?= (int) $pagination['from'] ?>-<?= (int) $pagination['to'] ?></span>
                <span class="hidrante-form-badge is-soft">Pagina <?= (int) $pagination['current_page'] ?> de <?= (int) $pagination['last_page'] ?></span>
            </div>
        </div>
    </section>

    <section class="card hidrante-listing-card">
        <div class="hidrante-form-divider">
            <h3>Filtros da listagem</h3>
            <p>Refine a pesquisa por numero, endereco, equipe responsavel, municipio e condicao operacional.</p>
        </div>

        <form method="GET" action="/hidrantes" class="filters-grid hidrante-listing-filters">
            <label>Busca
                <input
                    type="text"
                    name="q"
                    value="<?= e($filters['q'] ?? '') ?>"
                    placeholder="Numero, endereco ou equipe responsavel"
                >
            </label>

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
                    <?php foreach (($bairros ?? []) as $bairro): ?>
                        <option value="<?= e((string) $bairro['id']) ?>" <?= (string) ($filters['bairro_id'] ?? '') === (string) $bairro['id'] ? 'selected' : '' ?>>
                            <?= e($bairro['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="field-help hidrante-listing-bairro-help" id="filter-bairro-help" data-default-message="Selecione um municipio para listar todos os bairros disponiveis.">
                    <?= !empty($filters['municipio_id']) ? 'Escolha um bairro especifico ou mantenha "Todos" para pesquisar no municipio inteiro.' : 'Selecione um municipio para listar todos os bairros disponiveis.' ?>
                </span>
            </label>

            <div class="actions-inline hidrante-listing-actions">
                <button type="submit">Filtrar</button>
                <a class="btn-secondary" href="/hidrantes">Limpar</a>
                <a class="btn-secondary" href="<?= e(build_csv_url($filters)) ?>">Baixar CSV</a>

                <?php if (in_array($perfil, ['admin', 'gestor'], true)): ?>
                    <a class="btn-secondary" href="/hidrantes/novo">Novo hidrante</a>
                <?php endif; ?>
            </div>
        </form>
    </section>

    <section class="card hidrante-listing-card">
        <div class="table-header hidrante-listing-results-header">
            <div>
                <strong>Total:</strong> <?= (int) $pagination['total'] ?>
            </div>
            <div>
                Exibindo <?= (int) $pagination['from'] ?>-<?= (int) $pagination['to'] ?>
            </div>
        </div>

        <div class="hidrante-table-shell">
            <table class="hidrante-table">
                <thead>
                <tr>
                    <th>Numero</th>
                    <th>Municipio</th>
                    <th>Bairro</th>
                    <th>Status</th>
                    <th>Tipo</th>
                    <th>Area</th>
                    <th>Atualizado em</th>
                    <th>Acoes</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($hidrantes)): ?>
                    <tr>
                        <td colspan="8" class="hidrante-table-empty">Nenhum hidrante encontrado.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($hidrantes as $hidrante): ?>
                        <tr>
                            <td data-label="Numero">
                                <div class="hidrante-table-primary"><?= e($hidrante['numero_hidrante']) ?></div>
                            </td>
                            <td data-label="Municipio"><?= e($hidrante['municipio_nome']) ?></td>
                            <td data-label="Bairro"><?= e($hidrante['bairro_nome'] ?? '-') ?></td>
                            <td data-label="Status">
                                <span class="hidrante-status-badge <?= e(hidrante_status_class($hidrante['status_operacional'] ?? '')) ?>">
                                    <?= e($hidrante['status_operacional']) ?>
                                </span>
                            </td>
                            <td data-label="Tipo"><?= e($hidrante['tipo_hidrante']) ?></td>
                            <td data-label="Area">
                                <span class="hidrante-table-chip"><?= e($hidrante['area']) ?></span>
                            </td>
                            <td data-label="Atualizado em">
                                <span class="hidrante-table-muted"><?= e($hidrante['atualizado_em']) ?></span>
                            </td>
                            <td data-label="Acoes">
                                <div class="actions-inline hidrante-table-actions">
                                    <button
                                        type="button"
                                        class="btn-secondary"
                                        data-hidrante-detail
                                        data-hidrante='<?= hidrante_detail_payload($hidrante) ?>'
                                    >
                                        Ver detalhe
                                    </button>
                                    <a class="btn-secondary" href="/hidrantes/<?= (int) $hidrante['id'] ?>/editar">Editar</a>

                                    <?php if (in_array($perfil, ['admin', 'gestor'], true)): ?>
                                        <form
                                            method="POST"
                                            action="/hidrantes/<?= (int) $hidrante['id'] ?>/excluir"
                                            data-confirm-submit
                                            data-confirm-message="Deseja realmente excluir este hidrante?"
                                        >
                                            <?= csrf_field() ?>
                                            <button type="submit">Excluir</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (($pagination['last_page'] ?? 1) > 1): ?>
            <nav class="pagination">
                <?php $current = (int) $pagination['current_page']; ?>
                <?php $last = (int) $pagination['last_page']; ?>

                <a class="btn-secondary <?= $current <= 1 ? 'is-disabled' : '' ?>" href="<?= $current > 1 ? e(build_page_url($current - 1, $filters)) : '#' ?>">
                    Anterior
                </a>

                <?php
                $start = max(1, $current - 2);
                $end = min($last, $current + 2);

                if ($start > 1): ?>
                    <a class="btn-secondary" href="<?= e(build_page_url(1, $filters)) ?>">1</a>
                    <?php if ($start > 2): ?>
                        <span class="pagination-ellipsis">...</span>
                    <?php endif; ?>
                <?php endif; ?>

                <?php for ($i = $start; $i <= $end; $i++): ?>
                    <a class="btn-secondary <?= $i === $current ? 'is-active' : '' ?>" href="<?= e(build_page_url($i, $filters)) ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($end < $last): ?>
                    <?php if ($end < $last - 1): ?>
                        <span class="pagination-ellipsis">...</span>
                    <?php endif; ?>
                    <a class="btn-secondary" href="<?= e(build_page_url($last, $filters)) ?>"><?= $last ?></a>
                <?php endif; ?>

                <a class="btn-secondary <?= $current >= $last ? 'is-disabled' : '' ?>" href="<?= $current < $last ? e(build_page_url($current + 1, $filters)) : '#' ?>">
                    Proxima
                </a>
            </nav>
        <?php endif; ?>
    </section>
</div>

<div class="modal-backdrop" id="hidrante-detail-modal" hidden>
    <div class="modal-card hidrante-detail-modal-card" role="dialog" aria-modal="true" aria-labelledby="hidrante-detail-title">
        <div class="modal-header">
            <div>
                <h2 id="hidrante-detail-title">Detalhes do hidrante</h2>
                <p class="modal-subtitle" id="hidrante-detail-subtitle">Confira todas as informacoes do registro selecionado.</p>
            </div>
            <button type="button" class="btn-secondary modal-close-button" data-hidrante-detail-close>Fechar</button>
        </div>

        <div class="modal-body hidrante-detail-modal-body">
            <div class="hidrante-detail-summary">
                <span class="hidrante-detail-badge" id="hidrante-detail-badge-number">-</span>
                <span class="hidrante-detail-badge is-soft" id="hidrante-detail-badge-status">-</span>
                <span class="hidrante-detail-badge is-soft" id="hidrante-detail-badge-region">-</span>
            </div>

            <dl class="modal-detail-grid hidrante-detail-grid" id="hidrante-detail-grid"></dl>

            <section class="hidrante-detail-section" id="hidrante-detail-map-section" hidden>
                <div class="hidrante-detail-section-header">
                    <strong>Mapa do hidrante</strong>
                    <span class="location-map-preview-coordinates" id="hidrante-detail-map-coordinates">-</span>
                </div>
                <iframe
                    id="hidrante-detail-map-frame"
                    class="location-map-frame hidrante-detail-map-frame"
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    title="Mapa do hidrante"
                ></iframe>
            </section>

            <section class="hidrante-detail-section">
                <div class="hidrante-detail-section-header">
                    <strong>Fotos anexadas</strong>
                    <span class="location-map-preview-coordinates" id="hidrante-detail-photo-count">0 foto(s)</span>
                </div>
                <div class="hidrante-detail-empty" id="hidrante-detail-photo-empty">Nenhuma foto cadastrada para este hidrante.</div>
                <div class="hidrante-detail-photo-grid" id="hidrante-detail-photo-grid"></div>
            </section>
        </div>

        <div class="modal-body hidrante-detail-modal-actions">
            <button type="button" class="btn-secondary" data-hidrante-detail-close>Fechar</button>
        </div>
    </div>
</div>
