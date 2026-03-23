<?php
$baseStylesheets = [
    'globals/variables.css',
    'globals/reset.css',
    'globals/base.css',
];
$stylesheets = array_values(array_unique(array_merge(
    $baseStylesheets,
    $pageStylesheets ?? []
)));
$headLinks = array_values(array_unique($headLinks ?? []));
$pageScripts = array_values(array_unique($scripts ?? []));
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Documento') ?></title>
    <?php foreach ($stylesheets as $stylesheet): ?>
        <link rel="stylesheet" href="<?= e(css_asset($stylesheet)) ?>">
    <?php endforeach; ?>
    <?php foreach ($headLinks as $headLink): ?>
        <link rel="stylesheet" href="<?= e($headLink) ?>">
    <?php endforeach; ?>
</head>
<body class="document-layout">
<?= $content ?>
<?php foreach ($pageScripts as $script): ?>
    <script src="<?= e(js_asset($script)) ?>" defer></script>
<?php endforeach; ?>
</body>
</html>
