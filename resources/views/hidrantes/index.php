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
    ], static fn($value) => $value !== null && $value !== '');

    return '/hidrantes?' . http_build_query($query);
}
?>

<h1><?= e($title) ?></h1>

<section class="card">
    <form method="GET" action="/hidrantes" class="filters-grid">
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
                    <option value="<?= e($status) ?>" <?= ($filters['status_operacional'] ?? '') === $status ? 'selected' : '' ?>>
                        <?= e($status) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>Município
            <select name="municipio_id">
                <option value="">Todos</option>
                <?php foreach ($municipios as $municipio): ?>
                    <option value="<?= e((string) $municipio['id']) ?>" <?= (string) ($filters['municipio_id'] ?? '') === (string) $municipio['id'] ? 'selected' : '' ?>>
                        <?= e($municipio['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <div class="actions-inline">
            <button type="submit">Filtrar</button>
            <a class="btn-secondary" href="/hidrantes">Limpar</a>

            <?php if (in_array($perfil, ['admin', 'gestor'], true)): ?>
                <a class="btn-secondary" href="/hidrantes/novo">Novo hidrante</a>
            <?php endif; ?>
        </div>
    </form>
</section>

<section class="card">
    <div class="table-header">
        <div>
            <strong>Total:</strong> <?= (int) $pagination['total'] ?>
        </div>
        <div>
            Exibindo <?= (int) $pagination['from'] ?>–<?= (int) $pagination['to'] ?>
        </div>
    </div>

    <table>
        <thead>
        <tr>
            <th>Número</th>
            <th>Município</th>
            <th>Bairro</th>
            <th>Status</th>
            <th>Tipo</th>
            <th>Área</th>
            <th>Atualizado em</th>
            <th>Ações</th>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($hidrantes)): ?>
            <tr>
                <td colspan="8">Nenhum hidrante encontrado.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($hidrantes as $hidrante): ?>
                <tr>
                    <td><?= e($hidrante['numero_hidrante']) ?></td>
                    <td><?= e($hidrante['municipio_nome']) ?></td>
                    <td><?= e($hidrante['bairro_nome'] ?? '-') ?></td>
                    <td><?= e($hidrante['status_operacional']) ?></td>
                    <td><?= e($hidrante['tipo_hidrante']) ?></td>
                    <td><?= e($hidrante['area']) ?></td>
                    <td><?= e($hidrante['atualizado_em']) ?></td>
                    <td>
                        <div class="actions-inline">
                            <a class="btn-secondary" href="/hidrantes/<?= (int) $hidrante['id'] ?>/editar">Editar</a>

                            <?php if (in_array($perfil, ['admin', 'gestor'], true)): ?>
                                <form method="POST" action="/hidrantes/<?= (int) $hidrante['id'] ?>/excluir" onsubmit="return confirm('Deseja realmente excluir este hidrante?');">
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
                Próxima
            </a>
        </nav>
    <?php endif; ?>
</section>