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

        return $referencia !== '' ? 'Hidrante ID ' . $referencia : 'Hidrante nao identificado';
    }

    if ($entidade === 'usuarios') {
        if (!empty($item['usuario_nome_referencia'])) {
            return 'Usuario ' . $item['usuario_nome_referencia'];
        }

        return $referencia !== '' ? 'Usuario ID ' . $referencia : 'Usuario nao identificado';
    }

    if ($entidade === 'bairros') {
        if (!empty($item['bairro_nome_referencia'])) {
            if (!empty($item['bairro_municipio_referencia'])) {
                return 'Bairro ' . $item['bairro_nome_referencia'] . ' / ' . $item['bairro_municipio_referencia'];
            }

            return 'Bairro ' . $item['bairro_nome_referencia'];
        }

        return $referencia !== '' ? 'Bairro ID ' . $referencia : 'Bairro nao identificado';
    }

    if ($entidade === 'relatorios') {
        return $referencia !== '' ? 'Relatorio ' . $referencia : 'Relatorio sem referencia';
    }

    if ($entidade === '') {
        return $referencia !== '' ? 'Referencia ' . $referencia : 'Sem referencia';
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
?>

<h1><?= e($title) ?></h1>
<section class="card">
    <form method="GET" action="/historico" class="filters-grid">
        <label>Nome do usuario
            <input type="text" name="usuario_nome" value="<?= e((string) ($filters['usuario_nome'] ?? '')) ?>">
        </label>
        <label>Acao
            <input type="text" name="acao" value="<?= e((string) ($filters['acao'] ?? '')) ?>">
        </label>
        <div class="actions-inline">
            <button type="submit">Filtrar</button>
            <a class="btn-secondary" href="/historico">Limpar</a>
        </div>
    </form>
</section>
<section class="card">
    <div class="table-header">
        <div>
            <strong>Total:</strong> <?= (int) $pagination['total'] ?>
        </div>
        <div>
            Exibindo <?= (int) $pagination['from'] ?>-<?= (int) $pagination['to'] ?>
        </div>
    </div>

    <table>
        <thead>
        <tr><th>Data</th><th>Usuario</th><th>Acao</th><th>Entidade</th><th>Detalhes</th><th>Acoes</th></tr>
        </thead>
        <tbody>
        <?php if (empty($items)): ?>
            <tr>
                <td colspan="6">Nenhum registro encontrado.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($items as $item): ?>
                <?php $registroRelacionado = historico_registro_relacionado($item); ?>
                <tr>
                    <td><?= e($item['data_acao']) ?></td>
                    <td><?= e($item['usuario_nome_snapshot']) ?></td>
                    <td><?= e($item['acao']) ?></td>
                    <td><?= e($item['entidade'] ?? '-') ?></td>
                    <td><?= e(historico_detalhes_resumidos($item['detalhes'] ?? null)) ?></td>
                    <td>
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
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>

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
                Proxima
            </a>
        </nav>
    <?php endif; ?>
</section>

<div class="modal-backdrop" id="historico-detail-modal" hidden>
    <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="historico-detail-title">
        <div class="modal-header">
            <div>
                <h2 id="historico-detail-title">Detalhes do historico</h2>
                <p class="modal-subtitle" id="historico-detail-date">-</p>
            </div>
            <button type="button" class="modal-close-button" data-modal-close>Fechar</button>
        </div>

        <div class="modal-body">
            <dl class="modal-detail-grid">
                <div class="modal-detail-item">
                    <dt>Usuario</dt>
                    <dd id="historico-detail-user">-</dd>
                </div>
                <div class="modal-detail-item">
                    <dt>Acao</dt>
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
    </div>
</div>
