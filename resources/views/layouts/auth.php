<?php

use App\Core\Session;

$stylesheets = [
    'globals/variables.css',
    'globals/reset.css',
    'globals/base.css',
    'components/cards.css',
    'components/alerts.css',
    'components/forms.css',
    'components/buttons.css',
    'pages/auth.css',
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Login') ?></title>
    <?php foreach ($stylesheets as $stylesheet): ?>
        <link rel="stylesheet" href="<?= e(css_asset($stylesheet)) ?>">
    <?php endforeach; ?>
</head>
<body class="auth-page">
    <main class="auth-wrapper">
        <?php if ($error = Session::getFlash('error')): ?>
            <div class="alert alert-error"><?= e($error) ?></div>
        <?php endif; ?>
        <?php if ($success = Session::getFlash('success')): ?>
            <div class="alert alert-success"><?= e($success) ?></div>
        <?php endif; ?>
        <?= $content ?>
    </main>
</body>
</html>
