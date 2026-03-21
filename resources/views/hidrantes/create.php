<?php
$isEdit = !empty($hidrante);

function old_or_value(?array $hidrante, string $key, string $default = ''): string
{
    return e((string) ($hidrante[$key] ?? $default));
}
?>

<h1><?= e($title) ?></h1>

<section class="card">
    <form method="POST" action="<?= e($formAction ?? '/hidrantes/salvar') ?>" enctype="multipart/form-data" class="form-grid cols-2">
        <label>Número do hidrante
            <input type="text" name="numero_hidrante" required value="<?= old_or_value($hidrante, 'numero_hidrante') ?>">
        </label>

        <label>Equipe responsável
            <input type="text" name="equipe_responsavel" required value="<?= old_or_value($hidrante, 'equipe_responsavel') ?>">
        </label>

        <label>Área
            <select name="area" required>
                <?php
                $areaAtual = $hidrante['area'] ?? '';
                foreach (['urbano', 'industrial', 'rural'] as $item):
                ?>
                    <option value="<?= e($item) ?>" <?= $areaAtual === $item ? 'selected' : '' ?>><?= e($item) ?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>Existe no local
            <select name="existe_no_local" required>
                <?php
                $valorAtual = $hidrante['existe_no_local'] ?? '';
                foreach (['sim', 'nao'] as $item):
                ?>
                    <option value="<?= e($item) ?>" <?= $valorAtual === $item ? 'selected' : '' ?>><?= $item === 'nao' ? 'não' : 'sim' ?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>Tipo do hidrante
            <select name="tipo_hidrante" required>
                <?php
                $valorAtual = $hidrante['tipo_hidrante'] ?? '';
                $tipos = [
                    'coluna' => 'coluna',
                    'subterraneo' => 'subterrâneo',
                    'parede' => 'parede',
                    'outro' => 'outro',
                ];
                foreach ($tipos as $value => $label):
                ?>
                    <option value="<?= e($value) ?>" <?= $valorAtual === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>Acessibilidade
            <select name="acessibilidade" required>
                <?php
                $valorAtual = $hidrante['acessibilidade'] ?? '';
                foreach (['sim', 'nao'] as $item):
                ?>
                    <option value="<?= e($item) ?>" <?= $valorAtual === $item ? 'selected' : '' ?>><?= $item === 'nao' ? 'não' : 'sim' ?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>Tampo/conexões
            <select name="tampo_conexoes" required>
                <?php
                $valorAtual = $hidrante['tampo_conexoes'] ?? '';
                $opcoes = [
                    'integra' => 'íntegra',
                    'danificadas' => 'danificadas',
                    'ausentes' => 'ausentes',
                ];
                foreach ($opcoes as $value => $label):
                ?>
                    <option value="<?= e($value) ?>" <?= $valorAtual === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>Tampas ausentes
            <input type="text" name="tampas_ausentes" value="<?= old_or_value($hidrante, 'tampas_ausentes') ?>">
        </label>

        <label>Caixa de proteção
            <select name="caixa_protecao" required>
                <?php
                $valorAtual = $hidrante['caixa_protecao'] ?? '';
                foreach (['sim', 'nao'] as $item):
                ?>
                    <option value="<?= e($item) ?>" <?= $valorAtual === $item ? 'selected' : '' ?>><?= $item === 'nao' ? 'não' : 'sim' ?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>Condição da caixa
            <select name="condicao_caixa">
                <option value="">-- selecione --</option>
                <?php
                $valorAtual = $hidrante['condicao_caixa'] ?? '';
                foreach (['boa', 'regular', 'ruim'] as $item):
                ?>
                    <option value="<?= e($item) ?>" <?= $valorAtual === $item ? 'selected' : '' ?>><?= e($item) ?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>Presença de água
            <select name="presenca_agua_interior" required>
                <?php
                $valorAtual = $hidrante['presenca_agua_interior'] ?? '';
                foreach (['sim', 'nao'] as $item):
                ?>
                    <option value="<?= e($item) ?>" <?= $valorAtual === $item ? 'selected' : '' ?>><?= $item === 'nao' ? 'não' : 'sim' ?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>Teste realizado
            <select name="teste_realizado" required>
                <?php
                $valorAtual = $hidrante['teste_realizado'] ?? '';
                foreach (['sim', 'nao'] as $item):
                ?>
                    <option value="<?= e($item) ?>" <?= $valorAtual === $item ? 'selected' : '' ?>><?= $item === 'nao' ? 'não' : 'sim' ?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>Resultado do teste
            <select name="resultado_teste">
                <option value="">-- selecione --</option>
                <?php
                $valorAtual = $hidrante['resultado_teste'] ?? '';
                $opcoes = [
                    'funcionando normalmente' => 'funcionando normalmente',
                    'vazamento' => 'vazamento',
                    'vazao insuficiente' => 'vazão insuficiente',
                    'nao funcionou' => 'não funcionou',
                ];
                foreach ($opcoes as $value => $label):
                ?>
                    <option value="<?= e($value) ?>" <?= $valorAtual === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>Status operacional
            <select name="status_operacional" required>
                <?php
                $valorAtual = $hidrante['status_operacional'] ?? '';
                $opcoes = [
                    'operante' => 'operante',
                    'operante com restricao' => 'operante com restrição',
                    'inoperante' => 'inoperante',
                ];
                foreach ($opcoes as $value => $label):
                ?>
                    <option value="<?= e($value) ?>" <?= $valorAtual === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>Município
            <select name="municipio_id" id="municipio_id" required>
                <option value="">Selecione</option>
                <?php foreach ($municipios as $municipio): ?>
                    <option value="<?= e((string) $municipio['id']) ?>" <?= (string) ($hidrante['municipio_id'] ?? '') === (string) $municipio['id'] ? 'selected' : '' ?>>
                        <?= e($municipio['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>Bairro
            <select name="bairro_id" id="bairro_id">
                <option value="">Selecione</option>
                <?php foreach (($bairros ?? []) as $bairro): ?>
                    <option value="<?= e((string) $bairro['id']) ?>" <?= (string) ($hidrante['bairro_id'] ?? '') === (string) $bairro['id'] ? 'selected' : '' ?>>
                        <?= e($bairro['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label class="col-span-2">Endereço
            <input type="text" name="endereco" required value="<?= old_or_value($hidrante, 'endereco') ?>">
        </label>

        <label>Latitude
            <input type="text" name="latitude" value="<?= old_or_value($hidrante, 'latitude') ?>">
        </label>

        <label>Longitude
            <input type="text" name="longitude" value="<?= old_or_value($hidrante, 'longitude') ?>">
        </label>

        <label class="col-span-2">Fotos (até 3 imagens)
            <input type="file" name="fotos[]" multiple accept=".jpg,.jpeg,.png,.webp">
        </label>

        <?php if ($isEdit): ?>
            <div class="col-span-2">
                <strong>Fotos atuais:</strong>
                <div class="actions-inline">
                    <?php foreach (['foto_01', 'foto_02', 'foto_03'] as $fotoCampo): ?>
                        <?php if (!empty($hidrante[$fotoCampo])): ?>
                            <a class="btn-secondary" target="_blank" href="/storage/uploads/hidrantes/<?= e($hidrante[$fotoCampo]) ?>">
                                <?= e($fotoCampo) ?>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="col-span-2 actions-inline">
            <button type="submit"><?= $isEdit ? 'Atualizar' : 'Salvar' ?></button>
            <a class="btn-secondary" href="/hidrantes">Cancelar</a>
        </div>
    </form>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const municipio = document.getElementById('municipio_id');
    const bairro = document.getElementById('bairro_id');
    const bairroSelecionado = '<?= e((string) ($hidrante['bairro_id'] ?? '')) ?>';

    municipio.addEventListener('change', async () => {
        bairro.innerHTML = '<option value="">Carregando...</option>';

        if (!municipio.value) {
            bairro.innerHTML = '<option value="">Selecione</option>';
            return;
        }

        const response = await fetch(`/api/bairros/municipio/${municipio.value}`);
        const items = await response.json();

        bairro.innerHTML = '<option value="">Selecione</option>';

        items.forEach(item => {
            const option = document.createElement('option');
            option.value = item.id;
            option.textContent = item.nome;

            if (String(item.id) === String(bairroSelecionado)) {
                option.selected = true;
            }

            bairro.appendChild(option);
        });
    });
});
</script>