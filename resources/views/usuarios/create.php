<?php
$isEdit = (bool) ($isEdit ?? false);
$usuario = $usuario ?? null;
?>

<h1><?= e($title) ?></h1>
<section class="card">
    <form method="POST" action="<?= e($formAction ?? '/usuarios/salvar') ?>" class="form-grid cols-2">
        <?= csrf_field() ?>
        <label class="col-span-2">Nome
            <input type="text" name="nome" required value="<?= e((string) ($usuario['nome'] ?? '')) ?>">
        </label>
        <label>Matricula funcional
            <input type="text" name="matricula_funcional" required value="<?= e((string) ($usuario['matricula_funcional'] ?? '')) ?>">
        </label>

        <?php if (!$isEdit): ?>
            <label>Senha
                <input type="password" name="senha" required>
            </label>
        <?php else: ?>
            <div>
                <strong>Senha</strong>
                <p>A alteracao de senha e feita pela acao "Senha" na listagem de usuarios.</p>
            </div>
        <?php endif; ?>

        <label>Perfil
            <select name="perfil" required>
                <?php foreach (['admin', 'gestor', 'operador'] as $perfil): ?>
                    <option value="<?= e($perfil) ?>" <?= ($usuario['perfil'] ?? '') === $perfil ? 'selected' : '' ?>>
                        <?= e($perfil) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Status
            <select name="status" required>
                <?php foreach (['ativo', 'inativo'] as $status): ?>
                    <option value="<?= e($status) ?>" <?= ($usuario['status'] ?? 'ativo') === $status ? 'selected' : '' ?>>
                        <?= e($status) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <div class="col-span-2 actions-inline">
            <button type="submit"><?= $isEdit ? 'Atualizar' : 'Salvar' ?></button>
            <a class="btn-secondary" href="/usuarios">Cancelar</a>
        </div>
    </form>
</section>
