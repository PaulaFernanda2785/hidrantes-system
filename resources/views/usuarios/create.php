<?php
$isEdit = (bool) ($isEdit ?? false);
$usuario = $usuario ?? null;
?>

<div class="management-page">
    <section class="card management-card management-hero">
        <div class="management-header">
            <div class="management-header-copy">
                <p class="management-eyebrow"><?= $isEdit ? 'Atualização de acesso' : 'Cadastro de acesso' ?></p>
                <h1><?= e($title) ?></h1>
                <p class="management-description">
                    <?= $isEdit
                        ? 'Atualize os dados cadastrais, perfil e status do usuário selecionado sem sair do fluxo administrativo.'
                        : 'Crie novos usuários com os perfis corretos para manter a operação do sistema organizada e segura.' ?>
                </p>
            </div>
            <div class="management-badges">
                <span class="management-badge"><?= $isEdit ? 'Modo edição' : 'Novo cadastro' ?></span>
                <span class="management-badge is-soft">Perfis: admin, gestor e operador</span>
            </div>
        </div>
    </section>

    <section class="card management-card management-form-card">
        <div class="management-section-head">
            <h3>Dados do usuário</h3>
            <p>Preencha as informações obrigatórias para salvar o cadastro e liberar o acesso ao sistema.</p>
        </div>

        <form method="POST" action="<?= e($formAction ?? '/usuarios/salvar') ?>" class="form-grid cols-2 management-form-grid">
            <?= csrf_field() ?>

            <label class="col-span-2">Nome
                <input type="text" name="nome" required value="<?= e((string) ($usuario['nome'] ?? '')) ?>">
            </label>

            <label>Matrícula funcional
                <input type="text" name="matricula_funcional" required value="<?= e((string) ($usuario['matricula_funcional'] ?? '')) ?>">
            </label>

            <?php if (!$isEdit): ?>
                <label>Senha
                    <input type="password" name="senha" required>
                </label>
            <?php else: ?>
                <div class="management-form-note">
                    <strong>Senha</strong>
                    <p>A alteração de senha é feita pela ação "Senha" na listagem de usuários.</p>
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
                <button type="submit"><?= $isEdit ? 'Atualizar usuário' : 'Salvar usuário' ?></button>
                <a class="btn-secondary" href="/usuarios">Cancelar</a>
            </div>
        </form>
    </section>
</div>
