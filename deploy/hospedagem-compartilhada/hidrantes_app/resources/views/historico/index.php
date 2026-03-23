<?php
$pagination = $pagination ?? [
    'current_page' => 1,
    'per_page' => 15,
    'total' => 0,
    'last_page' => 1,
    'from' => 0,
    'to' => 0,
];

function build_historico_page_url(int $page, array $filters): string
{
    $query = array_filter([
        'page' => $page,
        'usuario_nome' => $filters['usuario_nome'] ?? '',
        'acao' => $filters['acao'] ?? '',
    ], static fn($value) => $value !== null && $value !== '');

    return '/historico?' . http_build_query($query);
}

function historico_registro_relacionado(array $item): string
{
    $entidade = trim((string) ($item['entidade'] ?? ''));
    $referencia = trim((string) ($item['referencia_registro'] ?? ''));

    if ($entidade === 'hidrantes') {
        if (!empty($item['hidrante_numero_referencia'])) {
            return 'Hidrante ' . $item['hidrante_numero_referencia'];
        }

        return $referencia !== '' ? 'Hidrante ID ' . $referencia : 'Hidrante não identificado';
    }

    if ($entidade === 'usuarios') {
        if (!empty($item['usuario_nome_referencia'])) {
            return 'Usuário ' . $item['usuario_nome_referencia'];
        }

        return $referencia !== '' ? 'Usuário ID ' . $referencia : 'Usuário não identificado';
    }

    if ($entidade === 'bairros') {
        if (!empty($item['bairro_nome_referencia'])) {
            if (!empty($item['bairro_municipio_referencia'])) {
                return 'Bairro ' . $item['bairro_nome_referencia'] . ' / ' . $item['bairro_municipio_referencia'];
            }

            return 'Bairro ' . $item['bairro_nome_referencia'];
        }

        return $referencia !== '' ? 'Bairro ID ' . $referencia : 'Bairro não identificado';
    }

    if ($entidade === 'relatorios') {
        return $referencia !== '' ? 'Relatório ' . $referencia : 'Relatório sem referência';
    }

    if ($entidade === '') {
        return $referencia !== '' ? 'Referência ' . $referencia : 'Sem referência';
    }

    return $referencia !== '' ? ucfirst($entidade) . ' ' . $referencia : ucfirst($entidade);
}

function historico_detalhes_resumidos(?string $detalhes, int $limit = 70): string
{
    $texto = trim((string) $detalhes);

    if ($texto === '') {
        return '-';
    }

    if (mb_strlen($texto) <= $limit) {
        return $texto;
    }

    return mb_substr($texto, 0, $limit - 3) . '...';
}

function historico_acao_class(?string $acao): string
{
    return match (strtolower(trim((string) $acao))) {
        'cadastrar' => 'is-cadastrar',
        'editar' => 'is-editar',
        'deletar', 'excluir' => 'is-deletar',
        default => 'is-neutral',
    };
}

function historico_entidade_class(?string $entidade): string
{
    return match (strtolower(trim((string) $entidade))) {
        'usuarios' => 'is-usuarios',
        'hidrantes' => 'is-hidrantes',
        'bairros' => 'is-bairros',
        'relatorios' => 'is-relatorios',
        default => 'is-neutral',
    };
}
?>

<div class="management-page">
    <section class="card management-card management-hero">
        <div class="management-header">
            <div class="management-header-copy">
                <p class="management-eyebrow">Auditoria de operações</p>
                <h1><?= e($title) ?></h1>
                <p class="management-description">
                    Consulte o histórico de ações do sistema, filtre por usuário e ação e acompanhe o detalhamento de cada registro auditado.
                </p>
            </div>
            <div class="management-badges">
                <span class="management-badge">Total: <?= (int) $pagination['total'] ?></span>
                <span class="management-badge is-soft">Exibindo <?= (int) $pagination['from'] ?>-<?= (int) $pagination['to'] ?></span>
                <span class="management-badge is-soft">Página <?= (int) $pagination['current_page'] ?> de <?= (int) $pagination['last_page'] ?></span>
            </div>
        </div>
    </section>

    <section class="card management-card">
        <div class="management-section-head">
            <h3>Filtros do histórico</h3>
            <p>Pesquise pelo nome do usuário e pela ação executada para localizar eventos específicos com mais rapidez.</p>
        </div>

        <form method="GET" action="/historico" class="filters-grid management-filters cols-2">
            <label>Nome do usuário
                <input type="text" name="usuario_nome" value="<?= e((string) ($filters['usuario_nome'] ?? '')) ?>" placeholder="Digite o nome do usuário">
            </label>
            <label>Ação
                <input type="text" name="acao" value="<?= e((string) ($filters['acao'] ?? '')) ?>" placeholder="Ex.: cadastrar, editar, deletar">
            </label>
            <div class="management-actions col-span-2">
                <button type="submit">Filtrar</button>
                <a class="btn-secondary" href="/historico">Limpar</a>
            </div>
        </form>
    </section>

    <section class="card management-card">
        <div class="table-header management-results-header">
            <div>
                <strong>Total:</strong> <?= (int) $pagination['total'] ?>
            </div>
            <div>
                Exibindo <?= (int) $pagination['from'] ?>-<?= (int) $pagination['to'] ?>
            </div>
        </div>

        <div class="management-table-shell">
            <table class="management-table">
                <thead>
                <tr>
                    <th>Data</th>
                    <th>Usuário</th>
                    <th>Ação</th>
                    <th>Entidade</th>
                    <th>Detalhes</th>
                    <th>Ações</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($items)): ?>
                    <tr>
                        <td colspan="6" class="management-empty">Nenhum registro encontrado.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($items as $item): ?>
                        <?php $registroRelacionado = historico_registro_relacionado($item); ?>
                        <tr>
                            <td data-label="Data">
                                <span class="management-table-muted"><?= e($item['data_acao']) ?></span>
                            </td>
                            <td data-label="Usuário">
                                <div class="management-table-primary"><?= e($item['usuario_nome_snapshot']) ?></div>
                            </td>
                            <td data-label="Ação">
                                <span class="management-chip <?= e(historico_acao_class($item['acao'] ?? '')) ?>"><?= e($item['acao']) ?></span>
                            </td>
                            <td data-label="Entidade">
                                <span class="management-chip <?= e(historico_entidade_class($item['entidade'] ?? '')) ?>"><?= e($item['entidade'] ?? '-') ?></span>
                            </td>
                            <td data-label="Detalhes">
                                <span class="management-table-muted"><?= e(historico_detalhes_resumidos($item['detalhes'] ?? null)) ?></span>
                            </td>
                            <td data-label="Ações">
                                <div class="actions-inline management-table-actions">
                                    <button
                                        type="button"
                                        class="btn-secondary history-detail-trigger"
                                        data-history-detail
                                        data-data-acao="<?= e((string) ($item['data_acao'] ?? '-')) ?>"
                                        data-usuario="<?= e((string) ($item['usuario_nome_snapshot'] ?? '-')) ?>"
                                        data-acao="<?= e((string) ($item['acao'] ?? '-')) ?>"
                                        data-entidade="<?= e((string) ($item['entidade'] ?? '-')) ?>"
                                        data-registro="<?= e($registroRelacionado) ?>"
                                        data-detalhes="<?= e((string) ($item['detalhes'] ?? 'Sem detalhes informados.')) ?>"
                                    >
                                        Ver detalhe
                                    </button>
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

                <a class="btn-secondary <?= $current <= 1 ? 'is-disabled' : '' ?>" href="<?= $current > 1 ? e(build_historico_page_url($current - 1, $filters)) : '#' ?>">
                    Anterior
                </a>

                <?php
                $start = max(1, $current - 2);
                $end = min($last, $current + 2);

                if ($start > 1): ?>
                    <a class="btn-secondary" href="<?= e(build_historico_page_url(1, $filters)) ?>">1</a>
                    <?php if ($start > 2): ?>
                        <span class="pagination-ellipsis">...</span>
                    <?php endif; ?>
                <?php endif; ?>

                <?php for ($i = $start; $i <= $end; $i++): ?>
                    <a class="btn-secondary <?= $i === $current ? 'is-active' : '' ?>" href="<?= e(build_historico_page_url($i, $filters)) ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($end < $last): ?>
                    <?php if ($end < $last - 1): ?>
                        <span class="pagination-ellipsis">...</span>
                    <?php endif; ?>
                    <a class="btn-secondary" href="<?= e(build_historico_page_url($last, $filters)) ?>"><?= $last ?></a>
                <?php endif; ?>

                <a class="btn-secondary <?= $current >= $last ? 'is-disabled' : '' ?>" href="<?= $current < $last ? e(build_historico_page_url($current + 1, $filters)) : '#' ?>">
                    Próxima
                </a>
            </nav>
        <?php endif; ?>
    </section>
</div>

<div class="modal-backdrop" id="historico-detail-modal" hidden>
    <div class="modal-card management-modal-card" role="dialog" aria-modal="true" aria-labelledby="historico-detail-title">
        <div class="modal-header">
            <div>
                <h2 id="historico-detail-title">Detalhes do histórico</h2>
                <p class="modal-subtitle" id="historico-detail-date">-</p>
            </div>
            <button type="button" class="btn-secondary modal-close-button" data-modal-close>Fechar</button>
        </div>

        <div class="modal-body">
            <dl class="modal-detail-grid">
                <div class="modal-detail-item">
                    <dt>Usuário</dt>
                    <dd id="historico-detail-user">-</dd>
                </div>
                <div class="modal-detail-item">
                    <dt>Ação</dt>
                    <dd id="historico-detail-action">-</dd>
                </div>
                <div class="modal-detail-item">
                    <dt>Entidade</dt>
                    <dd id="historico-detail-entity">-</dd>
                </div>
                <div class="modal-detail-item">
                    <dt>Registro relacionado</dt>
                    <dd id="historico-detail-record">-</dd>
                </div>
                <div class="modal-detail-item modal-detail-item-full">
                    <dt>Detalhes</dt>
                    <dd id="historico-detail-description">-</dd>
                </div>
            </dl>
        </div>

        <div class="modal-body management-form-actions">
            <button type="button" class="btn-secondary" data-modal-close>Fechar</button>
        </div>
    </div>
</div>
