<h1><?= e($title) ?></h1>
<section class="card">
    <p><strong>Usuario:</strong> <?= e((string) ($usuario['nome'] ?? '')) ?></p>
    <p><strong>Matricula:</strong> <?= e((string) ($usuario['matricula_funcional'] ?? '')) ?></p>

    <form method="POST" action="<?= e($formAction ?? '') ?>" class="form-grid">
        <?= csrf_field() ?>
        <label>Nova senha
            <input type="password" name="nova_senha" required>
        </label>
        <label>Confirmacao da senha
            <input type="password" name="confirmacao_senha" required>
        </label>
        <div class="actions-inline">
            <button type="submit">Salvar senha</button>
            <a class="btn-secondary" href="/usuarios">Cancelar</a>
        </div>
    </form>
</section>
