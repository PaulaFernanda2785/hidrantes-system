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
        'usuario_id' => $filters['usuario_id'] ?? '',
        'acao' => $filters['acao'] ?? '',
    ], static fn($value) => $value !== null && $value !== '');

    return '/historico?' . http_build_query($query);
}
?>

<h1><?= e($title) ?></h1>
<section class="card">
    <form method="GET" action="/historico" class="filters-grid">
        <label>ID do usuario
            <input type="text" name="usuario_id" value="<?= e((string) ($filters['usuario_id'] ?? '')) ?>">
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
        <tr><th>Data</th><th>Usuario</th><th>Acao</th><th>Entidade</th><th>Referencia</th><th>Detalhes</th></tr>
        </thead>
        <tbody>
        <?php if (empty($items)): ?>
            <tr>
                <td colspan="6">Nenhum registro encontrado.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= e($item['data_acao']) ?></td>
                    <td><?= e($item['usuario_nome_snapshot']) ?></td>
                    <td><?= e($item['acao']) ?></td>
                    <td><?= e($item['entidade'] ?? '-') ?></td>
                    <td><?= e($item['referencia_registro'] ?? '-') ?></td>
                    <td><?= e($item['detalhes'] ?? '-') ?></td>
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
