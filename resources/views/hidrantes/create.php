<?php

use App\Core\Session;

$isEdit = !empty($hidrante);
$auth = Session::get('auth', []);
$perfil = (string) ($auth['perfil'] ?? '');
$canEditNumeroHidrante = !$isEdit || in_array($perfil, ['admin', 'gestor'], true);
$uploadMaxFiles = (int) config('uploads.max_files', 3);
$uploadMaxFileSizeBytes = (int) config('uploads.max_file_size', 5 * 1024 * 1024);
$uploadMaxFileSizeMb = max(1, (int) round($uploadMaxFileSizeBytes / (1024 * 1024)));
$formIdempotencyScope = $isEdit ? 'hidrantes.update' : 'hidrantes.store';

function old_or_value(?array $hidrante, string $key, string $default = ''): string
{
    return e((string) ($hidrante[$key] ?? $default));
}

function hidrante_selected_tampas_ausentes(?array $hidrante): array
{
    $rawValue = trim((string) ($hidrante['tampas_ausentes'] ?? ''));

    if ($rawValue === '') {
        return [];
    }

    $allowed = [
        'direita' => true,
        'esquerda' => true,
        'central' => true,
    ];
    $selected = [];
    $tokens = preg_split('/\s*[,;|\/]\s*/', mb_strtolower($rawValue)) ?: [];

    foreach ($tokens as $token) {
        $normalized = trim((string) $token);

        if ($normalized === '' || !isset($allowed[$normalized])) {
            continue;
        }

        $selected[$normalized] = true;
    }

    return array_keys($selected);
}

$tampasAusentesSelecionadas = hidrante_selected_tampas_ausentes($hidrante);
$tampasAusentesOpcoes = [
    'direita' => 'Direita',
    'esquerda' => 'Esquerda',
    'central' => 'Central',
];
?>

<h1><?= e($title) ?></h1>

<section class="card hidrante-form-card">
    <form
        method="POST"
        action="<?= e($formAction ?? '/hidrantes/salvar') ?>"
        enctype="multipart/form-data"
        class="form-grid cols-2 hidrante-form"
        data-single-submit="true"
        data-submit-processing-text="Processando..."
        data-upload-max-files="<?= e((string) $uploadMaxFiles) ?>"
        data-upload-max-size-bytes="<?= e((string) $uploadMaxFileSizeBytes) ?>"
    >
        <?= csrf_field() ?>
        <?= idempotency_field($formIdempotencyScope) ?>
        <div class="col-span-2 hidrante-form-header">
            <div class="hidrante-form-header-copy">
                <p class="hidrante-form-eyebrow">Cadastro técnico</p>
                <h2><?= $isEdit ? 'Atualize os dados do hidrante com segurança e leitura clara.' : 'Preencha o cadastro completo do hidrante em uma única tela.' ?></h2>
                <p class="hidrante-form-description">
                    O formulário foi reorganizado para facilitar o preenchimento, a revisão em campo e o uso em telas menores.
                </p>
            </div>
            <div class="hidrante-form-header-badges">
                <span class="hidrante-form-badge"><?= $isEdit ? 'Modo edição' : 'Novo cadastro' ?></span>
                <span class="hidrante-form-badge is-soft">Fotos: até <?= e((string) $uploadMaxFiles) ?> de <?= e((string) $uploadMaxFileSizeMb) ?> MB</span>
                <span class="hidrante-form-badge is-soft">Mapa e GPS integrados</span>
            </div>
        </div>

        <div class="col-span-2 hidrante-form-divider">
            <h3>Identificação e operação</h3>
            <p>Dados principais para identificar o hidrante e registrar seu status atual.</p>
        </div>
        <label>Número do hidrante
            <input
                type="text"
                name="numero_hidrante"
                required
                value="<?= old_or_value($hidrante, 'numero_hidrante') ?>"
                <?= $canEditNumeroHidrante ? '' : 'readonly' ?>
            >
            <?php if (!$canEditNumeroHidrante): ?>
                <small class="field-help">O perfil operador pode atualizar o cadastro, mas não altera o número do hidrante.</small>
            <?php endif; ?>
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

        <div class="col-span-2 hidrante-form-divider">
            <h3>Condições físicas</h3>
            <p>Informações de acesso, caixa, tampas e observações estruturais do hidrante.</p>
        </div>

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

        <div class="hidrante-multi-select-field">
            <span class="hidrante-multi-select-label">Tampas ausentes</span>
            <div class="hidrante-multi-option-list" role="group" aria-label="Tampas ausentes">
                <?php foreach ($tampasAusentesOpcoes as $value => $label): ?>
                    <label class="hidrante-multi-option">
                        <input
                            type="checkbox"
                            name="tampas_ausentes[]"
                            value="<?= e($value) ?>"
                            <?= in_array($value, $tampasAusentesSelecionadas, true) ? 'checked' : '' ?>
                        >
                        <span><?= e($label) ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
            <small class="field-help">Selecione uma ou mais posições com tampa ausente.</small>
        </div>

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

        <div class="col-span-2 hidrante-form-divider">
            <h3>Teste e desempenho</h3>
            <p>Resultado do teste funcional e situação operacional observada no momento da vistoria.</p>
        </div>

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

        <div class="col-span-2 hidrante-form-divider">
            <h3>Localização e referência</h3>
            <p>Defina município, bairro, endereço e coordenadas para localizar o ponto com mais precisão.</p>
        </div>

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

        <div class="hidrante-bairro-field">
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

            <div class="hidrante-bairro-actions">
                <button type="button" class="btn-secondary hidrante-bairro-button" id="open-bairro-modal">
                    Cadastrar bairro
                </button>
                <button type="button" class="btn-secondary hidrante-bairro-button" id="edit-bairro-button">
                    Editar bairro
                </button>
                <p class="field-help hidrante-bairro-feedback" id="bairro-feedback">
                    Se não encontrar o bairro, cadastre um novo ou edite o bairro atualmente selecionado.
                </p>
            </div>
        </div>

        <label class="col-span-2">Endereço
            <input type="text" name="endereco" required value="<?= old_or_value($hidrante, 'endereco') ?>">
        </label>

        <label>Latitude
            <input type="text" name="latitude" id="latitude" value="<?= old_or_value($hidrante, 'latitude') ?>">
        </label>

        <label>Longitude
            <input type="text" name="longitude" id="longitude" value="<?= old_or_value($hidrante, 'longitude') ?>">
        </label>

        <div class="col-span-2 geolocation-actions">
            <button type="button" class="btn-secondary geolocation-button" id="use-current-location-button">
                Usar localização atual
            </button>
            <button type="button" class="btn-secondary geolocation-button" id="open-location-map-button">
                Ver no mapa
            </button>
            <button type="button" class="btn-secondary geolocation-button" id="clear-current-location-button">
                Limpar coordenadas
            </button>
            <p class="field-help geolocation-feedback" id="geolocation-feedback">
                No celular, use a localização atual para preencher latitude e longitude automaticamente.
            </p>
        </div>

        <div class="col-span-2 location-map-preview" id="location-map-preview" hidden>
            <div class="location-map-preview-header">
                <strong>Prévia do ponto no mapa</strong>
                <span class="location-map-preview-coordinates" id="location-map-preview-coordinates">-</span>
            </div>
            <iframe
                id="location-map-frame"
                class="location-map-frame"
                title="Prévia do ponto do hidrante no mapa"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
            ></iframe>
        </div>

        <div class="col-span-2 hidrante-form-divider">
            <h3>Registro fotográfico</h3>
            <p>Anexe imagens do hidrante usando arquivos, câmera do celular ou arrastar e soltar.</p>
        </div>

        <div class="col-span-2 hidrante-upload-panel">
            <span class="upload-dropzone-title">Anexar fotos</span>
            <div class="upload-dropzone" id="upload-dropzone">
                <p class="upload-dropzone-copy">Arraste até <?= e((string) $uploadMaxFiles) ?> imagens para esta área, selecione arquivos ou tire a foto pela câmera do celular. Cada arquivo pode ter até <?= e((string) $uploadMaxFileSizeMb) ?> MB.</p>
                <div class="upload-dropzone-actions">
                    <button type="button" class="upload-dropzone-button" id="upload-select-button">Selecionar imagens</button>
                    <button type="button" class="btn-secondary upload-dropzone-button" id="upload-camera-button">Usar câmera</button>
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
                <input
                    type="file"
                    id="upload-camera-input"
                    class="upload-input-hidden"
                    name="fotos_camera[]"
                    accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                    capture="environment"
                >
                <div class="upload-preview-grid" id="upload-preview-grid"></div>
                <p class="upload-empty" id="upload-empty-state">Você pode anexar até <?= e((string) $uploadMaxFiles) ?> fotos por hidrante, inclusive tirando fotos na hora pelo celular.</p>
                <p class="upload-limit-feedback" id="upload-limit-feedback" hidden>Limite de <?= e((string) $uploadMaxFiles) ?> fotos atingido. Remova uma imagem para anexar outra.</p>
            </div>
        </div>

        <?php if ($isEdit): ?>
            <div class="col-span-2 hidrante-current-photos">
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

        <div class="col-span-2 hidrante-form-footer">
            <div class="hidrante-form-footer-copy">
                <strong><?= $isEdit ? 'Revise as alterações antes de atualizar.' : 'Revise os dados antes de salvar.' ?></strong>
                <p>Todos os blocos do cadastro foram organizados para facilitar a conferência final em desktop e celular.</p>
            </div>
            <div class="actions-inline hidrante-form-footer-actions">
                <button type="submit"><?= $isEdit ? 'Atualizar' : 'Salvar' ?></button>
                <a class="btn-secondary" href="/hidrantes">Cancelar</a>
            </div>
        </div>
    </form>
</section>

<div class="modal-backdrop" id="bairro-modal" hidden>
    <div class="modal-card hidrante-bairro-modal-card" role="dialog" aria-modal="true" aria-labelledby="bairro-modal-title">
        <div class="modal-header">
            <div>
                <h2 id="bairro-modal-title">Cadastrar bairro</h2>
                <p class="modal-subtitle" id="bairro-modal-subtitle">O bairro será vinculado ao município atualmente selecionado.</p>
            </div>
            <button type="button" class="modal-close-button" data-bairro-modal-close>Fechar</button>
        </div>

        <div class="modal-body">
            <form id="bairro-create-form" class="form-grid">
                <label>Município selecionado
                    <input type="text" id="bairro-modal-municipio" readonly>
                </label>

                <label>Nome do bairro
                    <input type="text" id="bairro-modal-nome" maxlength="150" required>
                </label>

                <p class="field-help hidrante-bairro-modal-feedback" id="bairro-modal-feedback"></p>

                <div class="actions-inline">
                    <button type="submit" id="bairro-create-submit">Salvar bairro</button>
                    <button type="button" class="btn-secondary" data-bairro-modal-close>Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>
