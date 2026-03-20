<h1><?= e($title) ?></h1>
<section class="card">
    <form method="GET" action="/historico" class="filters-grid">
        <label>ID do usuário
            <input type="text" name="usuario_id" value="<?= e((string) ($filters['usuario_id'] ?? '')) ?>">
        </label>
        <label>Ação
            <input type="text" name="acao" value="<?= e((string) ($filters['acao'] ?? '')) ?>">
        </label>
        <div class="actions-inline">
            <button type="submit">Filtrar</button>
            <a class="btn-secondary" href="/historico">Limpar</a>
        </div>
    </form>
</section>
<section class="card">
    <table>
        <thead>
        <tr><th>Data</th><th>Usuário</th><th>Ação</th><th>Entidade</th><th>Referência</th><th>Detalhes</th></tr>
        </thead>
        <tbody>
        <?php foreach ($items as $item): ?>
            <tr>
                <td><?= e($item['data_acao']) ?></td>
                <td><?= e($item['usuario_nome_snapshot']) ?></td>
                <td><?= e($item['acao']) ?></td>
                <td><?= e($item['entidade'] ?? '-') ?></td>
                <td><?= e($item['referencia_registro'] ?? '-') ?></td>
                <td><?= e($item['detalhes'] ?? '-') ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
