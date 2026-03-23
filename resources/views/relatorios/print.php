<?php
require_once base_path('resources/views/relatorios/partials/helpers.php');

$document = $document ?? [];
$statusMetrics = $document['statusMetrics'] ?? [
    'total' => count($items ?? []),
    'operante' => 0,
    'operante com restricao' => 0,
    'inoperante' => 0,
];
$filterSummary = $document['filterSummary'] ?? [];
$reportPages = $document['reportPages'] ?? [];
$generatedAt = (string) ($document['generatedAt'] ?? date('d/m/Y H:i'));
$generatedBy = (string) ($document['generatedBy'] ?? 'Sistema');
$documentCode = (string) ($document['documentCode'] ?? 'RT-SGH-001');
$documentVersion = (string) ($document['documentVersion'] ?? '1.0.0');
?>

<div class="report-document-view">
    <div class="report-preview-pages">
        <?php foreach ($reportPages as $page): ?>
            <?php
            $pageType = (string) ($page['type'] ?? 'summary');
            $pageItem = $page['item'] ?? null;
            $pagePhotos = $page['photos'] ?? [];
            $pageHeaderSubtitle = match ($pageType) {
                'cover' => 'Capa institucional e consolida&ccedil;&atilde;o t&eacute;cnica.',
                'summary' => 'Par&acirc;metros da emiss&atilde;o e sum&aacute;rio executivo.',
                'hidrante' => 'Ficha t&eacute;cnica individual.',
                'photos' => 'Registro fotogr&aacute;fico do hidrante.',
                default => 'Documento institucional para impress&atilde;o.',
            };
            ?>
            <article
                class="report-page report-page--<?= e($pageType) ?>"
                data-report-page="<?= (int) ($page['page_number'] ?? 0) ?>"
                data-report-type="<?= e($pageType) ?>"
            >
                <header class="report-page-header">
                    <div class="report-page-header-top">
                        <div class="report-page-brand">
                            <img src="/img/logos/logo.cbmpa.png" alt="CBMPA" class="report-page-brand-logo">
                            <div class="report-page-brand-copy">
                                <strong>Corpo de Bombeiros Militar do Estado do Par&aacute;</strong>
                                <span>Coordenadoria Estadual de Prote&ccedil;&atilde;o e Defesa Civil</span>
                                <small>Sistema de Gest&atilde;o de Hidrantes</small>
                            </div>
                        </div>
                        <div class="report-page-meta">
                            <span>C&oacute;digo <?= e($documentCode) ?></span>
                            <span>Vers&atilde;o <?= e($documentVersion) ?></span>
                            <span>Emitido em <?= e($generatedAt) ?></span>
                        </div>
                    </div>

                    <div class="report-page-header-copy">
                        <p class="management-eyebrow">Documento institucional</p>
                        <h2 class="report-page-title">Relat&oacute;rio t&eacute;cnico de hidrantes</h2>
                        <p class="modal-subtitle"><?= $pageHeaderSubtitle ?></p>
                    </div>
                </header>

                <div class="report-page-body">
                    <?php if ($pageType === 'cover'): ?>
                        <section class="report-cover-panel">
                            <span class="report-cover-mark">Documento institucional</span>
                            <div class="report-cover-seal">
                                <img src="/img/logos/logo.cbmpa.png" alt="CBMPA" class="report-cover-seal-logo">
                                <div class="report-cover-seal-copy">
                                    <strong>Relat&oacute;rio t&eacute;cnico institucional</strong>
                                    <span>Modelo padronizado para impress&atilde;o, protocolo e arquivamento</span>
                                </div>
                            </div>

                            <div class="report-cover-hero">
                                <div>
                                    <h1>Relat&oacute;rio t&eacute;cnico de hidrantes</h1>
                                    <p class="report-cover-lead">
                                        Documento institucional para an&aacute;lise operacional, rastreabilidade de inspe&ccedil;&atilde;o e encaminhamento administrativo dos hidrantes filtrados no sistema.
                                    </p>
                                </div>

                                <div class="report-cover-document-grid">
                                    <div class="report-cover-document-item">
                                        <span>C&oacute;digo documental</span>
                                        <strong><?= e($documentCode) ?></strong>
                                    </div>
                                    <div class="report-cover-document-item">
                                        <span>Vers&atilde;o</span>
                                        <strong><?= e($documentVersion) ?></strong>
                                    </div>
                                    <div class="report-cover-document-item">
                                        <span>Gerado em</span>
                                        <strong><?= e($generatedAt) ?></strong>
                                    </div>
                                    <div class="report-cover-document-item">
                                        <span>Respons&aacute;vel</span>
                                        <strong><?= e($generatedBy) ?></strong>
                                    </div>
                                </div>
                            </div>

                            <div class="report-page-grid report-page-grid--two">
                                <section class="report-page-section report-page-section--formal">
                                    <h3>Escopo do documento</h3>
                                    <p>
                                        O relat&oacute;rio consolida os hidrantes filtrados com dados de identifica&ccedil;&atilde;o, condi&ccedil;&atilde;o f&iacute;sica, teste, localiza&ccedil;&atilde;o e evid&ecirc;ncias fotogr&aacute;ficas em um padr&atilde;o documental uniforme.
                                    </p>
                                    <p>
                                        A pagina&ccedil;&atilde;o foi organizada para manter leitura t&eacute;cnica est&aacute;vel na pr&eacute;-visualiza&ccedil;&atilde;o e na impress&atilde;o, sem reflow inesperado entre as duas experi&ecirc;ncias.
                                    </p>
                                </section>

                                <section class="report-page-section report-page-section--formal">
                                    <h3>Painel executivo</h3>
                                    <div class="report-summary-grid">
                                        <article class="management-metric-card">
                                            <span class="management-metric-label">Total</span>
                                            <strong class="management-metric-value"><?= (int) $statusMetrics['total'] ?></strong>
                                        </article>
                                        <article class="management-metric-card is-operador">
                                            <span class="management-metric-label">Operantes</span>
                                            <strong class="management-metric-value"><?= (int) $statusMetrics['operante'] ?></strong>
                                        </article>
                                        <article class="management-metric-card is-gestor">
                                            <span class="management-metric-label">Restri&ccedil;&atilde;o</span>
                                            <strong class="management-metric-value"><?= (int) $statusMetrics['operante com restricao'] ?></strong>
                                        </article>
                                        <article class="management-metric-card">
                                            <span class="management-metric-label">Inoperantes</span>
                                            <strong class="management-metric-value"><?= (int) $statusMetrics['inoperante'] ?></strong>
                                        </article>
                                    </div>
                                </section>
                            </div>

                            <div class="report-cover-summary">
                                <strong>Refer&ecirc;ncia institucional</strong>
                                <p>Documento emitido pelo Sistema de Gest&atilde;o de Hidrantes para apoio t&eacute;cnico do CBMPA / CEDEC-PA.</p>
                            </div>
                        </section>
                    <?php elseif ($pageType === 'summary'): ?>
                        <section class="report-page-section report-page-section--formal">
                            <div class="report-sheet-header">
                                <div>
                                    <h3>Par&acirc;metros da emiss&atilde;o</h3>
                                    <p class="management-table-muted">Crit&eacute;rios aplicados para compor este documento institucional.</p>
                                </div>
                                <span class="management-chip is-relatorios">Documento consolidado</span>
                            </div>

                            <dl class="report-filter-grid">
                                <?php foreach ($filterSummary as $label => $value): ?>
                                    <div class="report-filter-item">
                                        <dt><?= e($label) ?></dt>
                                        <dd><?= e($value) ?></dd>
                                    </div>
                                <?php endforeach; ?>
                            </dl>
                        </section>

                        <section class="report-page-grid report-page-grid--two">
                            <section class="report-page-section report-page-section--formal">
                                <h3>Sum&aacute;rio das se&ccedil;&otilde;es</h3>
                                <div class="report-section-outline">
                                    <div class="report-outline-item">
                                        <strong>1. Identifica&ccedil;&atilde;o e opera&ccedil;&atilde;o</strong>
                                        <span>N&uacute;mero, equipe, &aacute;rea, tipo e status operacional.</span>
                                    </div>
                                    <div class="report-outline-item">
                                        <strong>2. Condi&ccedil;&otilde;es f&iacute;sicas</strong>
                                        <span>Acesso, caixa, tampas, conex&otilde;es e presen&ccedil;a de &aacute;gua.</span>
                                    </div>
                                    <div class="report-outline-item">
                                        <strong>3. Teste e desempenho</strong>
                                        <span>Execu&ccedil;&atilde;o do teste, resultado e leitura operacional.</span>
                                    </div>
                                    <div class="report-outline-item">
                                        <strong>4. Localiza&ccedil;&atilde;o e refer&ecirc;ncia</strong>
                                        <span>Munic&iacute;pio, bairro, endere&ccedil;o e coordenadas.</span>
                                    </div>
                                    <div class="report-outline-item">
                                        <strong>5. Registro fotogr&aacute;fico</strong>
                                        <span>P&aacute;gina exclusiva para fotos quando houver imagens cadastradas.</span>
                                    </div>
                                </div>
                            </section>

                            <section class="report-page-section report-page-section--formal">
                                <h3>Observa&ccedil;&atilde;o t&eacute;cnica</h3>
                                <p class="report-empty-note">
                                    A pr&eacute;-visualiza&ccedil;&atilde;o e a impress&atilde;o utilizam o mesmo documento renderizado, no mesmo padr&atilde;o visual, evitando diferen&ccedil;as de pagina&ccedil;&atilde;o entre a tela e o papel.
                                </p>
                                <p class="report-empty-note">
                                    Quando houver fotografias, elas s&atilde;o distribu&iacute;das em uma p&aacute;gina espec&iacute;fica para preservar legibilidade e rastreabilidade do registro.
                                </p>
                            </section>
                        </section>
                    <?php elseif ($pageType === 'hidrante' && is_array($pageItem)): ?>
                        <section class="report-page-section report-page-section--compact report-page-section--formal">
                            <div class="report-entry-header">
                                <div class="report-hydrant-heading">
                                    <p class="management-eyebrow">Ficha t&eacute;cnica</p>
                                    <h3>Hidrante <?= e(relatorio_value($pageItem['numero_hidrante'] ?? '')) ?></h3>
                                    <p class="management-table-muted">
                                        <?= e(relatorio_value($pageItem['municipio_nome'] ?? '')) ?><?php if (trim((string) ($pageItem['bairro_nome'] ?? '')) !== ''): ?> | <?= e($pageItem['bairro_nome']) ?><?php endif; ?>
                                    </p>
                                </div>
                                <div class="report-entry-meta">
                                    <span class="management-status-badge <?= e(relatorio_status_class($pageItem['status_operacional'] ?? '')) ?>">
                                        <?= e(relatorio_status_label($pageItem['status_operacional'] ?? '')) ?>
                                    </span>
                                    <span class="management-chip is-neutral">Atualizado em <?= e(relatorio_format_datetime($pageItem['atualizado_em'] ?? '')) ?></span>
                                </div>
                            </div>
                        </section>

                        <section class="report-page-grid report-page-grid--two">
                            <section class="report-page-section report-page-section--compact report-page-section--formal">
                                <h3>Identifica&ccedil;&atilde;o e opera&ccedil;&atilde;o</h3>
                                <dl class="report-detail-grid report-detail-grid--compact">
                                    <div class="report-detail-item">
                                        <dt>N&uacute;mero</dt>
                                        <dd><?= e(relatorio_value($pageItem['numero_hidrante'] ?? '')) ?></dd>
                                    </div>
                                    <div class="report-detail-item">
                                        <dt>Equipe respons&aacute;vel</dt>
                                        <dd><?= e(relatorio_value($pageItem['equipe_responsavel'] ?? '')) ?></dd>
                                    </div>
                                    <div class="report-detail-item">
                                        <dt>&Aacute;rea</dt>
                                        <dd><?= e(relatorio_value($pageItem['area'] ?? '')) ?></dd>
                                    </div>
                                    <div class="report-detail-item">
                                        <dt>Existe no local</dt>
                                        <dd><?= e(relatorio_value($pageItem['existe_no_local'] ?? '')) ?></dd>
                                    </div>
                                    <div class="report-detail-item detail-item-full">
                                        <dt>Tipo do hidrante</dt>
                                        <dd><?= e(relatorio_value($pageItem['tipo_hidrante'] ?? '')) ?></dd>
                                    </div>
                                </dl>
                            </section>

                            <section class="report-page-section report-page-section--compact report-page-section--formal">
                                <h3>Condi&ccedil;&otilde;es f&iacute;sicas</h3>
                                <dl class="report-detail-grid report-detail-grid--compact">
                                    <div class="report-detail-item">
                                        <dt>Acessibilidade</dt>
                                        <dd><?= e(relatorio_value($pageItem['acessibilidade'] ?? '')) ?></dd>
                                    </div>
                                    <div class="report-detail-item">
                                        <dt>Tampo e conex&otilde;es</dt>
                                        <dd><?= e(relatorio_value($pageItem['tampo_conexoes'] ?? '')) ?></dd>
                                    </div>
                                    <div class="report-detail-item">
                                        <dt>Tampas ausentes</dt>
                                        <dd><?= e(relatorio_value($pageItem['tampas_ausentes'] ?? '')) ?></dd>
                                    </div>
                                    <div class="report-detail-item">
                                        <dt>Caixa de prote&ccedil;&atilde;o</dt>
                                        <dd><?= e(relatorio_value($pageItem['caixa_protecao'] ?? '')) ?></dd>
                                    </div>
                                    <div class="report-detail-item">
                                        <dt>Condi&ccedil;&atilde;o da caixa</dt>
                                        <dd><?= e(relatorio_value($pageItem['condicao_caixa'] ?? '')) ?></dd>
                                    </div>
                                    <div class="report-detail-item">
                                        <dt>Presen&ccedil;a de &aacute;gua</dt>
                                        <dd><?= e(relatorio_value($pageItem['presenca_agua_interior'] ?? '')) ?></dd>
                                    </div>
                                </dl>
                            </section>

                            <section class="report-page-section report-page-section--compact report-page-section--formal">
                                <h3>Teste e desempenho</h3>
                                <dl class="report-detail-grid report-detail-grid--compact">
                                    <div class="report-detail-item">
                                        <dt>Teste realizado</dt>
                                        <dd><?= e(relatorio_value($pageItem['teste_realizado'] ?? '')) ?></dd>
                                    </div>
                                    <div class="report-detail-item detail-item-full">
                                        <dt>Resultado do teste</dt>
                                        <dd><?= e(relatorio_value($pageItem['resultado_teste'] ?? '')) ?></dd>
                                    </div>
                                    <div class="report-detail-item detail-item-full">
                                        <dt>Status operacional</dt>
                                        <dd><?= e(relatorio_status_label($pageItem['status_operacional'] ?? '')) ?></dd>
                                    </div>
                                </dl>
                            </section>

                            <section class="report-page-section report-page-section--compact report-page-section--formal">
                                <h3>Localiza&ccedil;&atilde;o e refer&ecirc;ncia</h3>
                                <dl class="report-detail-grid report-detail-grid--compact">
                                    <div class="report-detail-item">
                                        <dt>Munic&iacute;pio</dt>
                                        <dd><?= e(relatorio_value($pageItem['municipio_nome'] ?? '')) ?></dd>
                                    </div>
                                    <div class="report-detail-item">
                                        <dt>Bairro</dt>
                                        <dd><?= e(relatorio_value($pageItem['bairro_nome'] ?? '')) ?></dd>
                                    </div>
                                    <div class="report-detail-item detail-item-full">
                                        <dt>Endere&ccedil;o</dt>
                                        <dd><?= e(relatorio_value($pageItem['endereco'] ?? '')) ?></dd>
                                    </div>
                                    <div class="report-detail-item">
                                        <dt>Latitude</dt>
                                        <dd><?= e(relatorio_value($pageItem['latitude'] ?? '')) ?></dd>
                                    </div>
                                    <div class="report-detail-item">
                                        <dt>Longitude</dt>
                                        <dd><?= e(relatorio_value($pageItem['longitude'] ?? '')) ?></dd>
                                    </div>
                                    <div class="report-detail-item">
                                        <dt>Criado em</dt>
                                        <dd><?= e(relatorio_format_datetime($pageItem['criado_em'] ?? '')) ?></dd>
                                    </div>
                                    <div class="report-detail-item">
                                        <dt>Atualizado em</dt>
                                        <dd><?= e(relatorio_format_datetime($pageItem['atualizado_em'] ?? '')) ?></dd>
                                    </div>
                                </dl>
                            </section>
                        </section>
                    <?php elseif ($pageType === 'photos' && is_array($pageItem)): ?>
                        <section class="report-page-section report-page-section--compact report-page-section--formal">
                            <div class="report-entry-header">
                                <div class="report-hydrant-heading">
                                    <p class="management-eyebrow">Registro fotogr&aacute;fico</p>
                                    <h3>Hidrante <?= e(relatorio_value($pageItem['numero_hidrante'] ?? '')) ?></h3>
                                    <p class="management-table-muted">
                                        <?= e(relatorio_value($pageItem['municipio_nome'] ?? '')) ?><?php if (trim((string) ($pageItem['bairro_nome'] ?? '')) !== ''): ?> | <?= e($pageItem['bairro_nome']) ?><?php endif; ?>
                                    </p>
                                </div>
                                <span class="management-chip is-neutral">Fotos anexadas: <?= count($pagePhotos) ?></span>
                            </div>
                        </section>

                        <?php if ($pagePhotos === []): ?>
                            <section class="report-page-section report-page-section--compact report-page-section--formal">
                                <p class="report-empty-note">Nenhuma fotografia foi cadastrada para este hidrante.</p>
                            </section>
                        <?php else: ?>
                            <section class="report-photo-page-grid">
                                <?php foreach ($pagePhotos as $photo): ?>
                                    <figure class="report-photo-card report-photo-card--compact">
                                        <img src="<?= e($photo['url']) ?>" alt="<?= e($photo['label']) ?>" class="report-photo-image">
                                        <figcaption><?= e($photo['label']) ?></figcaption>
                                    </figure>
                                <?php endforeach; ?>
                            </section>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <footer class="report-page-footer">
                    <div class="report-page-footer-copy">
                        <strong>Documento t&eacute;cnico institucional</strong>
                        <span>CBMPA / CEDEC-PA | Sistema de Gest&atilde;o de Hidrantes</span>
                    </div>
                    <div class="report-page-footer-page">P&aacute;gina <?= (int) ($page['page_number'] ?? 0) ?> de <?= (int) ($page['total_pages'] ?? 0) ?></div>
                </footer>
            </article>
        <?php endforeach; ?>
    </div>
</div>
