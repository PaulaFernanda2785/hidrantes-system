<?php

use App\Core\Session;

$auth = Session::get('auth');
$perfil = $auth['perfil'] ?? '';

function menu_active(string $path): string
{
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

    if ($path === '/painel' && ($uri === '/' || $uri === '/painel')) {
        return 'active';
    }

    return $uri === $path || str_starts_with($uri, $path . '/') ? 'active' : '';
}
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
        <div class="sidebar-brand">
            <img src="/img/logos/logo.cbmpa.png" alt="CBMPA" class="sidebar-logo">
            <div class="sidebar-brand-text">
                <strong>Sistema de Hidrantes</strong>
                <span>CBMPA / CEDEC-PA</span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <a href="/painel" class="<?= menu_active('/painel') ?>">Painel</a>

            <a href="/hidrantes" class="<?= menu_active('/hidrantes') ?>">Hidrantes</a>

            <?php if ($perfil === 'admin'): ?>
                <a href="/usuarios" class="<?= menu_active('/usuarios') ?>">Usuários</a>
            <?php endif; ?>

            <?php if (in_array($perfil, ['admin', 'gestor'], true)): ?>
                <a href="/relatorios/hidrantes" class="<?= menu_active('/relatorios') ?>">Relatórios</a>
                <a href="/historico" class="<?= menu_active('/historico') ?>">Histórico</a>
            <?php endif; ?>
        </nav>

        <div class="sidebar-footer">
            <p>Corpo de Bombeiros Militar do Estado do Pará e Coordenadoria Estadual de Proteção e Defesa Civil.</p>
            <p>Versão: 1.0.0</p>
            <p>© 2026 Governo do Estado do Pará</p>
        </div>
    </aside>

    <div class="main">
        <header class="topbar">
            <div class="topbar-title">
                <strong><?= e($title ?? 'Sistema de Gestão de Hidrantes') ?></strong>
            </div>

            <div class="topbar-user">
                <span><?= e($auth['nome'] ?? '') ?></span>
                <span class="topbar-separator">|</span>
                <span>Perfil: <?= e($perfil) ?></span>
                <span class="topbar-separator">|</span>
                <form method="POST" action="/logout" class="topbar-user-form">
                    <?= csrf_field() ?>
                    <button type="submit" class="topbar-link-button">Sair</button>
                </form>
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
