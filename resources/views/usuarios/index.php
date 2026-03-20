<h1><?= e($title) ?></h1>
<section class="card">
    <form method="GET" action="/usuarios" class="filters-grid">
        <label>Nome
            <input type="text" name="nome" value="<?= e($nome ?? '') ?>">
        </label>
        <div class="actions-inline">
            <button type="submit">Filtrar</button>
            <a class="btn-secondary" href="/usuarios">Limpar</a>
            <a class="btn-secondary" href="/usuarios/novo">Novo usuário</a>
        </div>
    </form>
</section>
<section class="card">
    <table>
        <thead>
        <tr><th>Nome</th><th>Matrícula</th><th>Perfil</th><th>Status</th><th>Criado em</th></tr>
        </thead>
        <tbody>
        <?php foreach ($usuarios as $usuario): ?>
            <tr>
                <td><?= e($usuario['nome']) ?></td>
                <td><?= e($usuario['matricula_funcional']) ?></td>
                <td><?= e($usuario['perfil']) ?></td>
                <td><?= e($usuario['status']) ?></td>
                <td><?= e($usuario['criado_em']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
