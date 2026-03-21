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

function usuario_status_class(?string $status): string
{
    return match (strtolower(trim((string) $status))) {
        'ativo' => 'is-ativo',
        'inativo' => 'is-inativo',
        default => 'is-neutral',
    };
}

function usuario_perfil_class(?string $perfil): string
{
    return match (strtolower(trim((string) $perfil))) {
        'admin' => 'is-admin',
        'gestor' => 'is-gestor',
        'operador' => 'is-operador',
        default => 'is-neutral',
    };
}
?>

<div class="management-page">
    <section class="card management-card management-hero">
        <div class="management-header">
            <div class="management-header-copy">
                <p class="management-eyebrow">Administracao de acesso</p>
                <h1><?= e($title) ?></h1>
                <p class="management-description">
                    Gerencie perfis, acompanhe a distribuicao entre administradores, gestores e operadores e mantenha o controle de acesso do sistema atualizado.
                </p>
            </div>
            <div class="management-badges">
                <span class="management-badge">Total filtrado: <?= (int) $pagination['total'] ?></span>
                <span class="management-badge is-soft">Exibindo <?= (int) $pagination['from'] ?>-<?= (int) $pagination['to'] ?></span>
                <span class="management-badge is-soft">Pagina <?= (int) $pagination['current_page'] ?> de <?= (int) $pagination['last_page'] ?></span>
            </div>
        </div>
    </section>

    <section class="management-metric-grid">
        <article class="management-metric-card">
            <span class="management-metric-label">Total de usuarios</span>
            <strong class="management-metric-value"><?= e((string) ($metrics['total'] ?? 0)) ?></strong>
        </article>
        <article class="management-metric-card is-admin">
            <span class="management-metric-label">Perfil admin</span>
            <strong class="management-metric-value"><?= e((string) ($metrics['admins'] ?? 0)) ?></strong>
        </article>
        <article class="management-metric-card is-gestor">
            <span class="management-metric-label">Perfil gestor</span>
            <strong class="management-metric-value"><?= e((string) ($metrics['gestores'] ?? 0)) ?></strong>
        </article>
        <article class="management-metric-card is-operador">
            <span class="management-metric-label">Perfil operador</span>
            <strong class="management-metric-value"><?= e((string) ($metrics['operadores'] ?? 0)) ?></strong>
        </article>
    </section>

    <section class="card management-card">
        <div class="management-section-head">
            <h3>Filtros da listagem</h3>
            <p>Pesquise usuarios pelo nome e acesse rapidamente as acoes de edicao, senha e ativacao.</p>
        </div>

        <form method="GET" action="/usuarios" class="filters-grid management-filters cols-2">
            <label>Nome
                <input type="text" name="nome" value="<?= e($nome ?? '') ?>" placeholder="Digite o nome do usuario">
            </label>
            <div class="management-actions">
                <button type="submit">Filtrar</button>
                <a class="btn-secondary" href="/usuarios">Limpar</a>
                <a class="btn-secondary" href="/usuarios/novo">Novo usuario</a>
            </div>
        </form>
    </section>

    <section class="card management-card">
        <div class="table-header management-results-header">
            <div>
                <strong>Total filtrado:</strong> <?= (int) $pagination['total'] ?>
            </div>
            <div>
                Exibindo <?= (int) $pagination['from'] ?>-<?= (int) $pagination['to'] ?>
            </div>
        </div>

        <div class="management-table-shell">
            <table class="management-table">
                <thead>
                <tr>
                    <th>Nome</th>
                    <th>Matricula</th>
                    <th>Perfil</th>
                    <th>Status</th>
                    <th>Criado em</th>
                    <th>Acoes</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($usuarios)): ?>
                    <tr>
                        <td colspan="6" class="management-empty">Nenhum usuario encontrado.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($usuarios as $usuario): ?>
                        <?php $isCurrentUser = (int) $currentUserId === (int) $usuario['id']; ?>
                        <?php $targetStatus = $usuario['status'] === 'ativo' ? 'inativo' : 'ativo'; ?>
                        <tr>
                            <td data-label="Nome">
                                <div class="management-table-primary"><?= e($usuario['nome']) ?></div>
                            </td>
                            <td data-label="Matricula">
                                <span class="management-table-muted"><?= e($usuario['matricula_funcional']) ?></span>
                            </td>
                            <td data-label="Perfil">
                                <span class="management-chip <?= e(usuario_perfil_class($usuario['perfil'] ?? '')) ?>"><?= e($usuario['perfil']) ?></span>
                            </td>
                            <td data-label="Status">
                                <span class="management-status-badge <?= e(usuario_status_class($usuario['status'] ?? '')) ?>"><?= e($usuario['status']) ?></span>
                            </td>
                            <td data-label="Criado em">
                                <span class="management-table-muted"><?= e($usuario['criado_em']) ?></span>
                            </td>
                            <td data-label="Acoes">
                                <div class="actions-inline management-table-actions">
                                    <a class="btn-secondary" href="/usuarios/<?= (int) $usuario['id'] ?>/editar">Editar</a>
                                    <a class="btn-secondary" href="/usuarios/<?= (int) $usuario['id'] ?>/senha">Senha</a>

                                    <?php if ($isCurrentUser): ?>
                                        <span class="management-chip is-neutral">Conta atual</span>
                                    <?php else: ?>
                                        <form
                                            method="POST"
                                            action="/usuarios/<?= (int) $usuario['id'] ?>/status"
                                            data-confirm-submit
                                            data-confirm-message="Deseja realmente <?= $targetStatus === 'ativo' ? 'ativar' : 'inativar' ?> este usuario?"
                                        >
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
        </div>

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
</div>
