<div class="management-page">
    <section class="card management-card management-hero">
        <div class="management-header">
            <div class="management-header-copy">
                <p class="management-eyebrow">Seguranca de acesso</p>
                <h1><?= e($title) ?></h1>
                <p class="management-description">
                    Defina uma nova senha para o usuario selecionado mantendo o controle de acesso atualizado e seguro.
                </p>
            </div>
            <div class="management-badges">
                <span class="management-badge">Alteracao de senha</span>
                <span class="management-badge is-soft"><?= e((string) ($usuario['matricula_funcional'] ?? '-')) ?></span>
            </div>
        </div>
    </section>

    <section class="card management-card management-form-card">
        <div class="management-section-head">
            <h3>Usuario selecionado</h3>
            <p>Confira os dados abaixo e informe a nova senha para concluir a atualizacao.</p>
        </div>

        <div class="management-summary">
            <strong><?= e((string) ($usuario['nome'] ?? '')) ?></strong>
            <p>Matricula funcional: <?= e((string) ($usuario['matricula_funcional'] ?? '-')) ?></p>
        </div>

        <form method="POST" action="<?= e($formAction ?? '') ?>" class="form-grid management-form-grid">
            <?= csrf_field() ?>

            <label>Nova senha
                <input type="password" name="nova_senha" required>
            </label>

            <label>Confirmacao da senha
                <input type="password" name="confirmacao_senha" required>
            </label>

            <div class="actions-inline management-form-actions">
                <button type="submit">Salvar senha</button>
                <a class="btn-secondary" href="/usuarios">Cancelar</a>
            </div>
        </form>
    </section>
</div>
