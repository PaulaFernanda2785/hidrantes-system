<h1><?= e($title) ?></h1>
<section class="card">
    <form method="POST" action="/hidrantes/salvar" enctype="multipart/form-data" class="form-grid cols-2">
        <label>Número do hidrante<input type="text" name="numero_hidrante" required></label>
        <label>Equipe responsável<input type="text" name="equipe_responsavel" required></label>
        <label>Área
            <select name="area" required>
                <option value="urbano">urbano</option>
                <option value="industrial">industrial</option>
                <option value="rural">rural</option>
            </select>
        </label>
        <label>Existe no local
            <select name="existe_no_local" required>
                <option value="sim">sim</option>
                <option value="nao">não</option>
            </select>
        </label>
        <label>Tipo do hidrante
            <select name="tipo_hidrante" required>
                <option value="coluna">coluna</option>
                <option value="subterraneo">subterrâneo</option>
                <option value="parede">parede</option>
                <option value="outro">outro</option>
            </select>
        </label>
        <label>Acessibilidade
            <select name="acessibilidade" required>
                <option value="sim">sim</option>
                <option value="nao">não</option>
            </select>
        </label>
        <label>Tampo/conexões
            <select name="tampo_conexoes" required>
                <option value="integra">íntegra</option>
                <option value="danificadas">danificadas</option>
                <option value="ausentes">ausentes</option>
            </select>
        </label>
        <label>Tampas ausentes<input type="text" name="tampas_ausentes"></label>
        <label>Caixa de proteção
            <select name="caixa_protecao" required>
                <option value="sim">sim</option>
                <option value="nao">não</option>
            </select>
        </label>
        <label>Condição da caixa
            <select name="condicao_caixa">
                <option value="">-- selecione --</option>
                <option value="boa">boa</option>
                <option value="regular">regular</option>
                <option value="ruim">ruim</option>
            </select>
        </label>
        <label>Presença de água
            <select name="presenca_agua_interior" required>
                <option value="sim">sim</option>
                <option value="nao">não</option>
            </select>
        </label>
        <label>Teste realizado
            <select name="teste_realizado" required>
                <option value="sim">sim</option>
                <option value="nao">não</option>
            </select>
        </label>
        <label>Resultado do teste
            <select name="resultado_teste">
                <option value="">-- selecione --</option>
                <option value="funcionando normalmente">funcionando normalmente</option>
                <option value="vazamento">vazamento</option>
                <option value="vazao insuficiente">vazão insuficiente</option>
                <option value="nao funcionou">não funcionou</option>
            </select>
        </label>
        <label>Status operacional
            <select name="status_operacional" required>
                <option value="operante">operante</option>
                <option value="operante com restricao">operante com restrição</option>
                <option value="inoperante">inoperante</option>
            </select>
        </label>
        <label>Município
            <select name="municipio_id" id="municipio_id" required>
                <option value="">Selecione</option>
                <?php foreach ($municipios as $municipio): ?>
                    <option value="<?= e((string) $municipio['id']) ?>"><?= e($municipio['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Bairro
            <select name="bairro_id" id="bairro_id">
                <option value="">Selecione</option>
            </select>
        </label>
        <label class="col-span-2">Endereço<input type="text" name="endereco" required></label>
        <label>Latitude<input type="text" name="latitude"></label>
        <label>Longitude<input type="text" name="longitude"></label>
        <label class="col-span-2">Fotos (até 3 imagens)<input type="file" name="fotos[]" multiple accept=".jpg,.jpeg,.png,.webp"></label>
        <div class="col-span-2 actions-inline">
            <button type="submit">Salvar</button>
            <a class="btn-secondary" href="/hidrantes">Cancelar</a>
        </div>
    </form>
</section>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const municipio = document.getElementById('municipio_id');
  const bairro = document.getElementById('bairro_id');
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
      bairro.appendChild(option);
    });
  });
});
</script>
