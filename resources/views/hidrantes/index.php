<h1><?= e($title) ?></h1>

<section class="card">
    <form method="GET" action="/hidrantes" class="filters-grid">
        <label>Status
            <select name="status_operacional">
                <option value="">Todos</option>
                <?php foreach (['operante', 'operante com restricao', 'inoperante'] as $status): ?>
                    <option value="<?= e($status) ?>" <?= ($filters['status_operacional'] ?? '') === $status ? 'selected' : '' ?>>
                        <?= e($status) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>Município
            <select name="municipio_id">
                <option value="">Todos</option>
                <?php foreach ($municipios as $municipio): ?>
                    <option value="<?= e((string) $municipio['id']) ?>" <?= (string) ($filters['municipio_id'] ?? '') === (string) $municipio['id'] ? 'selected' : '' ?>>
                        <?= e($municipio['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <div class="actions-inline">
            <button type="submit">Filtrar</button>
            <a class="btn-secondary" href="/hidrantes">Limpar</a>
            <?php $auth = \App\Core\Session::get('auth'); ?>
            <?php if (in_array(($auth['perfil'] ?? ''), ['admin', 'gestor'], true)): ?>
                <a class="btn-secondary" href="/hidrantes/novo">Novo hidrante</a>
            <?php endif; ?>
        </div>
    </form>
</section>

<section class="card">
    <table>
        <thead>
        <tr>
            <th>Número</th>
            <th>Município</th>
            <th>Bairro</th>
            <th>Status</th>
            <th>Tipo</th>
            <th>Área</th>
            <th>Atualizado em</th>
            <th>Ações</th>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($hidrantes)): ?>
            <tr>
                <td colspan="8">Nenhum hidrante encontrado.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($hidrantes as $hidrante): ?>
                <tr>
                    <td><?= e($hidrante['numero_hidrante']) ?></td>
                    <td><?= e($hidrante['municipio_nome']) ?></td>
                    <td><?= e($hidrante['bairro_nome'] ?? '-') ?></td>
                    <td><?= e($hidrante['status_operacional']) ?></td>
                    <td><?= e($hidrante['tipo_hidrante']) ?></td>
                    <td><?= e($hidrante['area']) ?></td>
                    <td><?= e($hidrante['atualizado_em']) ?></td>
                    <td>
                        <?php $auth = \App\Core\Session::get('auth'); ?>
                        <div class="actions-inline">
                            <a class="btn-secondary" href="/hidrantes/<?= (int) $hidrante['id'] ?>/editar">Editar</a>

                            <?php if (in_array(($auth['perfil'] ?? ''), ['admin', 'gestor'], true)): ?>
                                <form method="POST" action="/hidrantes/<?= (int) $hidrante['id'] ?>/excluir" onsubmit="return confirm('Deseja realmente excluir este hidrante?');">
                                    <button type="submit">Excluir</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</section>