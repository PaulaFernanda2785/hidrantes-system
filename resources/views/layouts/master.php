<?php
use App\Core\Session;
$auth = Session::get('auth');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Sistema de Gestão de Hidrantes') ?></title>
    <link rel="stylesheet" href="/assets/app.css">
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <div class="brand">Sistema de Hidrantes</div>
        <nav>
            <a href="/painel">Painel</a>
            <a href="/hidrantes">Hidrantes</a>
            <a href="/relatorios/hidrantes">Relatórios</a>
            <a href="/usuarios">Usuários</a>
            <a href="/historico">Histórico</a>
        </nav>
    </aside>
    <div class="main">
        <header class="topbar">
            <div>
                <strong>CBMPA / CEDEC-PA</strong>
            </div>
            <div>
                <?= e($auth['nome'] ?? '') ?> | Perfil: <?= e($auth['perfil'] ?? '') ?> | <a href="/logout">Sair</a>
            </div>
        </header>
        <main class="content">
            <?php if ($error = Session::getFlash('error')): ?>
                <div class="alert alert-error"><?= e($error) ?></div>
            <?php endif; ?>
            <?php if ($success = Session::getFlash('success')): ?>
                <div class="alert alert-success"><?= e($success) ?></div>
            <?php endif; ?>
            <?= $content ?>
        </main>
    </div>
</div>
<script src="/assets/app.js"></script>
</body>
</html>
