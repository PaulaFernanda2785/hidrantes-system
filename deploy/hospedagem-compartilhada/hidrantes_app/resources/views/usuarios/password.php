<?php
$isSelfPassword = (bool) ($isSelfPassword ?? false);
$cancelUrl = (string) ($cancelUrl ?? '/usuarios');
$usuarioNome = (string) ($usuario['nome'] ?? '');
$usuarioMatricula = (string) ($usuario['matricula_funcional'] ?? '-');
$description = $isSelfPassword
    ? 'Atualize a sua senha para manter o acesso da conta protegido.'
    : 'Defina uma nova senha para o usuario selecionado, mantendo o controle de acesso atualizado e seguro.';
$sectionTitle = $isSelfPassword ? 'Minha conta' : 'Usuario selecionado';
$sectionDescription = $isSelfPassword
    ? 'Confirme sua senha atual e informe a nova senha para concluir a alteracao.'
    : 'Confira os dados abaixo e informe a nova senha para concluir a atualizacao.';
$badgeLabel = $isSelfPassword ? 'Minha senha' : 'Alteracao de senha';
?>

<div class="management-page">
    <section class="card management-card management-hero">
        <div class="management-header">
            <div class="management-header-copy">
                <p class="management-eyebrow">Seguranca de acesso</p>
                <h1><?= e($title) ?></h1>
                <p class="management-description">
                    <?= e($description) ?>
                </p>
            </div>
            <div class="management-badges">
                <span class="management-badge"><?= e($badgeLabel) ?></span>
                <span class="management-badge is-soft"><?= e($usuarioMatricula) ?></span>
            </div>
        </div>
    </section>

    <section class="card management-card management-form-card">
        <div class="management-section-head">
            <h3><?= e($sectionTitle) ?></h3>
            <p><?= e($sectionDescription) ?></p>
        </div>

        <div class="management-summary">
            <strong><?= e($usuarioNome) ?></strong>
            <p>Matricula funcional: <?= e($usuarioMatricula) ?></p>
        </div>

        <form method="POST" action="<?= e($formAction ?? '') ?>" class="form-grid management-form-grid">
            <?= csrf_field() ?>

            <?php if ($isSelfPassword): ?>
                <label>Senha atual
                    <input type="password" name="senha_atual" required autocomplete="current-password">
                </label>
            <?php endif; ?>

            <label>Nova senha
                <input type="password" name="nova_senha" required autocomplete="new-password">
            </label>

            <label>Confirmacao da senha
                <input type="password" name="confirmacao_senha" required autocomplete="new-password">
            </label>

            <div class="actions-inline management-form-actions">
                <button type="submit">Salvar senha</button>
                <a class="btn-secondary" href="<?= e($cancelUrl) ?>">Cancelar</a>
            </div>
        </form>
    </section>
</div>
