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
        <?= csrf_field() ?>
        <label>Numero do hidrante
            <input type="text" name="numero_hidrante" required value="<?= old_or_value($hidrante, 'numero_hidrante') ?>">
        </label>

        <label>Equipe responsavel
            <input type="text" name="equipe_responsavel" required value="<?= old_or_value($hidrante, 'equipe_responsavel') ?>">
        </label>

        <label>Area
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
                    <option value="<?= e($item) ?>" <?= $valorAtual === $item ? 'selected' : '' ?>><?= $item === 'nao' ? 'nao' : 'sim' ?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>Tipo do hidrante
            <select name="tipo_hidrante" required>
                <?php
                $valorAtual = $hidrante['tipo_hidrante'] ?? '';
                $tipos = [
                    'coluna' => 'coluna',
                    'subterraneo' => 'subterraneo',
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
                    <option value="<?= e($item) ?>" <?= $valorAtual === $item ? 'selected' : '' ?>><?= $item === 'nao' ? 'nao' : 'sim' ?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>Tampo/conexoes
            <select name="tampo_conexoes" required>
                <?php
                $valorAtual = $hidrante['tampo_conexoes'] ?? '';
                $opcoes = [
                    'integra' => 'integra',
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

        <label>Caixa de protecao
            <select name="caixa_protecao" required>
                <?php
                $valorAtual = $hidrante['caixa_protecao'] ?? '';
                foreach (['sim', 'nao'] as $item):
                ?>
                    <option value="<?= e($item) ?>" <?= $valorAtual === $item ? 'selected' : '' ?>><?= $item === 'nao' ? 'nao' : 'sim' ?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>Condicao da caixa
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

        <label>Presenca de agua
            <select name="presenca_agua_interior" required>
                <?php
                $valorAtual = $hidrante['presenca_agua_interior'] ?? '';
                foreach (['sim', 'nao'] as $item):
                ?>
                    <option value="<?= e($item) ?>" <?= $valorAtual === $item ? 'selected' : '' ?>><?= $item === 'nao' ? 'nao' : 'sim' ?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>Teste realizado
            <select name="teste_realizado" required>
                <?php
                $valorAtual = $hidrante['teste_realizado'] ?? '';
                foreach (['sim', 'nao'] as $item):
                ?>
                    <option value="<?= e($item) ?>" <?= $valorAtual === $item ? 'selected' : '' ?>><?= $item === 'nao' ? 'nao' : 'sim' ?></option>
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
                    'vazao insuficiente' => 'vazao insuficiente',
                    'nao funcionou' => 'nao funcionou',
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
                    'operante com restricao' => 'operante com restricao',
                    'inoperante' => 'inoperante',
                ];
                foreach ($opcoes as $value => $label):
                ?>
                    <option value="<?= e($value) ?>" <?= $valorAtual === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>Municipio
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

        <label class="col-span-2">Endereco
            <input type="text" name="endereco" required value="<?= old_or_value($hidrante, 'endereco') ?>">
        </label>

        <label>Latitude
            <input type="text" name="latitude" value="<?= old_or_value($hidrante, 'latitude') ?>">
        </label>

        <label>Longitude
            <input type="text" name="longitude" value="<?= old_or_value($hidrante, 'longitude') ?>">
        </label>

        <div class="col-span-2">
            <span class="upload-dropzone-title">Anexar fotos</span>
            <div class="upload-dropzone" id="upload-dropzone">
                <p class="upload-dropzone-copy">Arraste ate 3 imagens para esta area ou clique no botao para selecionar.</p>
                <div class="upload-dropzone-actions">
                    <button type="button" class="upload-dropzone-button" id="upload-select-button">Selecionar imagens</button>
                    <span class="upload-dropzone-meta" id="upload-selection-count">Nenhuma imagem selecionada.</span>
                </div>
                <input
                    type="file"
                    id="upload-fotos-input"
                    class="upload-input-hidden"
                    name="fotos[]"
                    multiple
                    accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                >
                <div class="upload-preview-grid" id="upload-preview-grid"></div>
                <p class="upload-empty" id="upload-empty-state">Voce pode anexar ate 3 fotos por hidrante.</p>
            </div>
        </div>

        <?php if ($isEdit): ?>
            <div class="col-span-2">
                <strong>Fotos atuais:</strong>
                <div class="actions-inline">
                    <?php foreach (['foto_01', 'foto_02', 'foto_03'] as $fotoCampo): ?>
                        <?php if (!empty($hidrante[$fotoCampo])): ?>
                            <a class="btn-secondary" target="_blank" rel="noopener noreferrer" href="/uploads/hidrantes/<?= e($hidrante[$fotoCampo]) ?>">
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
    const fileInput = document.getElementById('upload-fotos-input');
    const dropzone = document.getElementById('upload-dropzone');
    const selectButton = document.getElementById('upload-select-button');
    const previewGrid = document.getElementById('upload-preview-grid');
    const emptyState = document.getElementById('upload-empty-state');
    const selectionCount = document.getElementById('upload-selection-count');
    const maxFiles = 3;
    let selectedFiles = [];

    const syncInputFiles = () => {
        const transfer = new DataTransfer();
        selectedFiles.forEach((file) => transfer.items.add(file));
        fileInput.files = transfer.files;
    };

    const updateSelectionCount = () => {
        if (selectedFiles.length === 0) {
            selectionCount.textContent = 'Nenhuma imagem selecionada.';
            emptyState.hidden = false;
            return;
        }

        selectionCount.textContent = `${selectedFiles.length} imagem(ns) pronta(s) para envio.`;
        emptyState.hidden = true;
    };

    const renderPreviews = () => {
        previewGrid.innerHTML = '';

        selectedFiles.forEach((file, index) => {
            const card = document.createElement('div');
            card.className = 'upload-preview-card';

            const image = document.createElement('img');
            image.className = 'upload-preview-media';
            image.alt = file.name;
            image.src = URL.createObjectURL(file);
            image.addEventListener('load', () => URL.revokeObjectURL(image.src), { once: true });

            const name = document.createElement('div');
            name.className = 'upload-preview-name';
            name.textContent = file.name;

            const removeButton = document.createElement('button');
            removeButton.type = 'button';
            removeButton.className = 'btn-secondary upload-preview-remove';
            removeButton.textContent = 'Remover';
            removeButton.addEventListener('click', () => {
                selectedFiles = selectedFiles.filter((_, fileIndex) => fileIndex !== index);
                syncInputFiles();
                renderPreviews();
                updateSelectionCount();
            });

            card.appendChild(image);
            card.appendChild(name);
            card.appendChild(removeButton);
            previewGrid.appendChild(card);
        });
    };

    const mergeFiles = (incomingFiles) => {
        const validFiles = Array.from(incomingFiles).filter((file) => file.type.startsWith('image/'));

        if (validFiles.length === 0) {
            return;
        }

        const mergedFiles = [...selectedFiles, ...validFiles].slice(0, maxFiles);
        selectedFiles = mergedFiles;
        syncInputFiles();
        renderPreviews();
        updateSelectionCount();
    };

    selectButton.addEventListener('click', () => fileInput.click());

    fileInput.addEventListener('change', () => {
        mergeFiles(fileInput.files);
    });

    ['dragenter', 'dragover'].forEach((eventName) => {
        dropzone.addEventListener(eventName, (event) => {
            event.preventDefault();
            dropzone.classList.add('is-dragover');
        });
    });

    ['dragleave', 'drop'].forEach((eventName) => {
        dropzone.addEventListener(eventName, (event) => {
            event.preventDefault();
            dropzone.classList.remove('is-dragover');
        });
    });

    dropzone.addEventListener('drop', (event) => {
        const droppedFiles = event.dataTransfer?.files;
        if (!droppedFiles || droppedFiles.length === 0) {
            return;
        }

        mergeFiles(droppedFiles);
    });

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

    updateSelectionCount();
});
</script>
