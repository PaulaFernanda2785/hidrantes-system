<?php
$isEdit = (bool) ($isEdit ?? false);
$usuario = $usuario ?? null;
?>

<div class="management-page">
    <section class="card management-card management-hero">
        <div class="management-header">
            <div class="management-header-copy">
                <p class="management-eyebrow"><?= $isEdit ? 'Atualizacao de acesso' : 'Cadastro de acesso' ?></p>
                <h1><?= e($title) ?></h1>
                <p class="management-description">
                    <?= $isEdit
                        ? 'Atualize os dados cadastrais, perfil e status do usuario selecionado sem sair do fluxo administrativo.'
                        : 'Crie novos usuarios com os perfis corretos para manter a operacao do sistema organizada e segura.' ?>
                </p>
            </div>
            <div class="management-badges">
                <span class="management-badge"><?= $isEdit ? 'Modo edicao' : 'Novo cadastro' ?></span>
                <span class="management-badge is-soft">Perfis: admin, gestor e operador</span>
            </div>
        </div>
    </section>

    <section class="card management-card management-form-card">
        <div class="management-section-head">
            <h3>Dados do usuario</h3>
            <p>Preencha as informacoes obrigatorias para salvar o cadastro e liberar o acesso ao sistema.</p>
        </div>

        <form method="POST" action="<?= e($formAction ?? '/usuarios/salvar') ?>" class="form-grid cols-2 management-form-grid">
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
                <div class="management-form-note">
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

            <div class="col-span-2 actions-inline management-form-actions">
                <button type="submit"><?= $isEdit ? 'Atualizar usuario' : 'Salvar usuario' ?></button>
                <a class="btn-secondary" href="/usuarios">Cancelar</a>
            </div>
        </form>
    </section>
</div>
