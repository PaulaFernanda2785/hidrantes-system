<h1><?= e($title) ?></h1>
<section class="card">
    <form method="GET" action="/relatorios/hidrantes" class="filters-grid">
        <label>Status
            <select name="status_operacional">
                <option value="">Todos</option>
                <?php foreach (['operante', 'operante com restricao', 'inoperante'] as $status): ?>
                    <option value="<?= e($status) ?>" <?= ($filters['status_operacional'] ?? '') === $status ? 'selected' : '' ?>><?= e($status) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Município
            <select name="municipio_id">
                <option value="">Todos</option>
                <?php foreach ($municipios as $municipio): ?>
                    <option value="<?= e((string) $municipio['id']) ?>" <?= (string) ($filters['municipio_id'] ?? '') === (string) $municipio['id'] ? 'selected' : '' ?>><?= e($municipio['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <div class="actions-inline">
            <button type="submit">Gerar</button>
            <a class="btn-secondary" href="/relatorios/hidrantes">Limpar</a>
        </div>
    </form>
</section>
<section class="card">
    <table>
        <thead>
        <tr><th>Número</th><th>Município</th><th>Bairro</th><th>Status</th><th>Tipo</th><th>Área</th></tr>
        </thead>
        <tbody>
        <?php foreach ($items as $item): ?>
            <tr>
                <td><?= e($item['numero_hidrante']) ?></td>
                <td><?= e($item['municipio_nome']) ?></td>
                <td><?= e($item['bairro_nome'] ?? '-') ?></td>
                <td><?= e($item['status_operacional']) ?></td>
                <td><?= e($item['tipo_hidrante']) ?></td>
                <td><?= e($item['area']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
