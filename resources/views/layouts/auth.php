<?php use App\Core\Session; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Login') ?></title>
    <link rel="stylesheet" href="/assets/app.css">
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
