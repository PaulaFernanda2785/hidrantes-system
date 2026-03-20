<h1><?= e($title) ?></h1>
<section class="cards-grid">
    <article class="card"><h3>Total</h3><strong><?= e((string) ($metrics['total'] ?? 0)) ?></strong></article>
    <article class="card"><h3>Operantes</h3><strong><?= e((string) ($metrics['operantes'] ?? 0)) ?></strong></article>
    <article class="card"><h3>Com restrição</h3><strong><?= e((string) ($metrics['restricao'] ?? 0)) ?></strong></article>
    <article class="card"><h3>Inoperantes</h3><strong><?= e((string) ($metrics['inoperantes'] ?? 0)) ?></strong></article>
</section>
<section class="card">
    <h2>Mapa - Dados iniciais</h2>
    <p>Quantidade de pontos georreferenciados: <strong><?= count($mapPoints) ?></strong></p>
    <pre class="pre-scroll"><?= e(json_encode(array_slice($mapPoints, 0, 5), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></pre>
</section>
