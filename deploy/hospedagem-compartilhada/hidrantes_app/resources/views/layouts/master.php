<?php

use App\Core\Session;

$auth = Session::get('auth');
$perfil = $auth['perfil'] ?? '';
$baseStylesheets = [
    'globals/variables.css',
    'globals/reset.css',
    'globals/base.css',
    'layouts/layout.css',
    'layouts/sidebar.css',
    'layouts/topbar.css',
    'layouts/responsive.css',
    'components/cards.css',
    'components/alerts.css',
    'components/forms.css',
    'components/buttons.css',
    'components/modal.css',
    'components/tables.css',
    'components/pagination.css',
    'pages/auth.css',
    'pages/management.css',
    'pages/hidrantes.css',
];
$stylesheets = array_values(array_unique(array_merge(
    $baseStylesheets,
    $pageStylesheets ?? []
)));
$headLinks = array_values(array_unique($headLinks ?? []));
$externalScripts = array_values(array_unique($externalScripts ?? []));
$pageScripts = array_values(array_unique(array_merge([
    'core/app.js',
], $scripts ?? [])));

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
    <link rel="icon" href="/img/ico/logo.cbmpa.ico" type="image/x-icon">
    <?php foreach ($stylesheets as $stylesheet): ?>
        <link rel="stylesheet" href="<?= e(css_asset($stylesheet)) ?>">
    <?php endforeach; ?>
    <?php foreach ($headLinks as $headLink): ?>
        <link rel="stylesheet" href="<?= e($headLink) ?>">
    <?php endforeach; ?>
</head>
<body>
<div class="layout">
    <button type="button" class="sidebar-backdrop" data-sidebar-close hidden aria-hidden="true"></button>

    <aside class="sidebar" id="app-sidebar" aria-label="Menu principal">
        <div class="sidebar-head">
            <div class="sidebar-brand">
                <img src="/img/logos/logo.cbmpa.png" alt="CBMPA" class="sidebar-logo">
                <div class="sidebar-brand-text">
                    <strong>Sistema de Hidrantes</strong>
                    <span>CBMPA / CEDEC-PA</span>
                </div>
            </div>
            <button
                type="button"
                class="sidebar-close-button"
                data-sidebar-close
                aria-label="Fechar menu"
            >
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <nav class="sidebar-nav">
            <a href="/painel" class="<?= menu_active('/painel') ?>">Painel</a>

            <a href="/hidrantes" class="<?= menu_active('/hidrantes') ?>">Hidrantes</a>

            <a href="/minha-senha" class="<?= menu_active('/minha-senha') ?>">Alterar senha</a>

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
            <p>&copy; 2026 Governo do Estado do Pará</p>
        </div>
    </aside>

    <div class="main">
        <header class="topbar">
            <div class="topbar-main">
                <button
                    type="button"
                    class="topbar-menu-button"
                    data-sidebar-toggle
                    aria-controls="app-sidebar"
                    aria-expanded="false"
                    aria-label="Abrir menu principal"
                >
                    <span></span>
                    <span></span>
                    <span></span>
                </button>

                <div class="topbar-title">
                    <strong><?= e($title ?? 'Sistema de Gestão de Hidrantes') ?></strong>
                </div>
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

<?php foreach ($externalScripts as $externalScript): ?>
    <script src="<?= e($externalScript) ?>" defer></script>
<?php endforeach; ?>
<?php foreach ($pageScripts as $script): ?>
    <script src="<?= e(js_asset($script)) ?>" defer></script>
<?php endforeach; ?>
</body>
</html>

