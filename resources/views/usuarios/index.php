<?php
$pagination = $pagination ?? [
    'current_page' => 1,
    'per_page' => 15,
    'total' => 0,
    'last_page' => 1,
    'from' => 0,
    'to' => 0,
];

function build_usuario_page_url(int $page, ?string $nome): string
{
    $query = array_filter([
        'page' => $page,
        'nome' => $nome ?? '',
    ], static fn($value) => $value !== null && $value !== '');

    return '/usuarios?' . http_build_query($query);
}
?>

<h1><?= e($title) ?></h1>

<section class="cards-grid">
    <article class="card"><h3>Total de usuarios</h3><strong><?= e((string) ($metrics['total'] ?? 0)) ?></strong></article>
    <article class="card"><h3>Perfil admin</h3><strong><?= e((string) ($metrics['admins'] ?? 0)) ?></strong></article>
    <article class="card"><h3>Perfil gestor</h3><strong><?= e((string) ($metrics['gestores'] ?? 0)) ?></strong></article>
    <article class="card"><h3>Perfil operador</h3><strong><?= e((string) ($metrics['operadores'] ?? 0)) ?></strong></article>
</section>

<section class="card">
    <form method="GET" action="/usuarios" class="filters-grid">
        <label>Nome
            <input type="text" name="nome" value="<?= e($nome ?? '') ?>">
        </label>
        <div class="actions-inline">
            <button type="submit">Filtrar</button>
            <a class="btn-secondary" href="/usuarios">Limpar</a>
            <a class="btn-secondary" href="/usuarios/novo">Novo usuario</a>
        </div>
    </form>
</section>

<section class="card">
    <div class="table-header">
        <div>
            <strong>Total filtrado:</strong> <?= (int) $pagination['total'] ?>
        </div>
        <div>
            Exibindo <?= (int) $pagination['from'] ?>-<?= (int) $pagination['to'] ?>
        </div>
    </div>

    <table>
        <thead>
        <tr><th>Nome</th><th>Matricula</th><th>Perfil</th><th>Status</th><th>Criado em</th><th>Acoes</th></tr>
        </thead>
        <tbody>
        <?php if (empty($usuarios)): ?>
            <tr>
                <td colspan="6">Nenhum usuario encontrado.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($usuarios as $usuario): ?>
                <?php $isCurrentUser = (int) $currentUserId === (int) $usuario['id']; ?>
                <?php $targetStatus = $usuario['status'] === 'ativo' ? 'inativo' : 'ativo'; ?>
                <tr>
                    <td><?= e($usuario['nome']) ?></td>
                    <td><?= e($usuario['matricula_funcional']) ?></td>
                    <td><?= e($usuario['perfil']) ?></td>
                    <td><?= e($usuario['status']) ?></td>
                    <td><?= e($usuario['criado_em']) ?></td>
                    <td>
                        <div class="actions-inline">
                            <a class="btn-secondary" href="/usuarios/<?= (int) $usuario['id'] ?>/editar">Editar</a>
                            <a class="btn-secondary" href="/usuarios/<?= (int) $usuario['id'] ?>/senha">Senha</a>

                            <?php if ($isCurrentUser): ?>
                                <span class="btn-secondary">Conta atual</span>
                            <?php else: ?>
                                <form method="POST" action="/usuarios/<?= (int) $usuario['id'] ?>/status" onsubmit="return confirm('Deseja realmente <?= $targetStatus === 'ativo' ? 'ativar' : 'inativar' ?> este usuario?');">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="status" value="<?= e($targetStatus) ?>">
                                    <button type="submit"><?= $targetStatus === 'ativo' ? 'Ativar' : 'Inativar' ?></button>
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

            <a class="btn-secondary <?= $current <= 1 ? 'is-disabled' : '' ?>" href="<?= $current > 1 ? e(build_usuario_page_url($current - 1, $nome ?? '')) : '#' ?>">
                Anterior
            </a>

            <?php
            $start = max(1, $current - 2);
            $end = min($last, $current + 2);

            if ($start > 1): ?>
                <a class="btn-secondary" href="<?= e(build_usuario_page_url(1, $nome ?? '')) ?>">1</a>
                <?php if ($start > 2): ?>
                    <span class="pagination-ellipsis">...</span>
                <?php endif; ?>
            <?php endif; ?>

            <?php for ($i = $start; $i <= $end; $i++): ?>
                <a class="btn-secondary <?= $i === $current ? 'is-active' : '' ?>" href="<?= e(build_usuario_page_url($i, $nome ?? '')) ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <?php if ($end < $last): ?>
                <?php if ($end < $last - 1): ?>
                    <span class="pagination-ellipsis">...</span>
                <?php endif; ?>
                <a class="btn-secondary" href="<?= e(build_usuario_page_url($last, $nome ?? '')) ?>"><?= $last ?></a>
            <?php endif; ?>

            <a class="btn-secondary <?= $current >= $last ? 'is-disabled' : '' ?>" href="<?= $current < $last ? e(build_usuario_page_url($current + 1, $nome ?? '')) : '#' ?>">
                Proxima
            </a>
        </nav>
    <?php endif; ?>
</section>
