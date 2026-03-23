# Documento Tecnico - Servicos

## Papel da camada de servicos

Os services implementam a regra de negocio do sistema. Eles recebem dados ja capturados pelos controllers, aplicam validacoes, normalizam entrada, consultam repositories e, quando necessario, registram auditoria.

## Servicos implementados

### `AuthService`

Responsabilidades:

- autenticar usuario por matricula funcional;
- validar status `ativo`;
- verificar senha hash;
- iniciar sessao autenticada;
- regenerar sessao;
- registrar `login` e `logout` em auditoria.

Dependencias:

- `UsuarioRepository`
- `PasswordService`
- `AuditService`

### `LoginThrottleService`

Responsabilidades:

- limitar tentativas de login por IP e combinacao matricula+IP;
- bloquear novas tentativas apos 5 falhas;
- manter janela de 15 minutos;
- persistir estado em arquivo JSON com `flock`.

Observacao:

- este servico nao usa banco de dados; o estado e gravado em `storage/framework/security/login_throttle.json`.

### `PainelService`

Responsabilidades:

- fornecer metricas agregadas de hidrantes;
- fornecer pontos georreferenciados para o mapa.

Dependencia:

- `HidranteRepository`

### `HidranteService`

Responsabilidades:

- listar hidrantes com filtros;
- localizar hidrante por ID;
- validar e normalizar dados do formulario;
- impedir numero duplicado;
- garantir coerencia entre bairro e municipio;
- validar coordenadas via `GeoService`;
- processar upload via `UploadService`;
- aplicar regra de perfil do operador na edicao;
- executar exclusao logica;
- registrar auditoria de cadastro, edicao e exclusao.

Dependencias:

- `HidranteRepository`
- `BairroRepository`
- `UploadService`
- `GeoService`
- `AuditService`

### `UsuarioService`

Responsabilidades:

- listar e medir usuarios;
- validar nome, matricula, perfil e status;
- criar usuario com hash de senha;
- atualizar dados cadastrais;
- impedir que o usuario altere o proprio perfil/status por esta tela;
- trocar senha com politica minima;
- ativar e inativar usuarios;
- registrar auditoria.

Dependencias:

- `UsuarioRepository`
- `PasswordService`
- `AuditService`

### `BairroService`

Responsabilidades:

- validar cadastro e edicao de bairro;
- evitar duplicidade por municipio;
- reativar bairro inativo quando o mesmo nome ja existir;
- registrar auditoria de cadastro, edicao e reativacao.

Dependencias:

- `BairroRepository`
- `AuditService`

### `RelatorioService`

Responsabilidades:

- delegar a montagem do conjunto filtrado de hidrantes para relatorio.

Dependencia:

- `HidranteRepository`

Observacao:

- o servico e enxuto e ainda nao registra auditoria de geracao de relatorio.

### `HistoricoService`

Responsabilidades:

- consultar e paginar historico de usuario.

Dependencia:

- `HistoricoRepository`

### `AuditService`

Responsabilidades:

- gravar eventos em `historico_usuario`;
- padronizar captura do ator, acao, entidade, referencia e IP de origem.

Dependencia:

- `HistoricoRepository`

### `UploadService`

Responsabilidades:

- validar existencia de arquivos;
- aceitar apenas `jpeg`, `png` e `webp`;
- limitar tamanho a 5 MB por arquivo;
- limitar dimensoes e quantidade de pixels;
- renomear arquivo com nome aleatorio;
- armazenar ate 3 imagens por hidrante.

### `GeoService`

Responsabilidades:

- validar faixa de latitude e longitude;
- montar URL de Google Maps.

Estado atual de uso:

- a validacao e utilizada em `HidranteService`;
- a geracao de URL nao e explorada diretamente nos controllers atuais, pois o frontend monta as URLs de navegacao.

### `PasswordService`

Responsabilidades:

- encapsular `password_hash`;
- encapsular `password_verify`.

## Padrao de colaboracao entre servicos

Fluxo tipico:

1. Controller recebe requisicao.
2. Service valida e normaliza.
3. Repository persiste ou consulta.
4. Service registra auditoria quando aplicavel.
5. Controller devolve resposta adequada.

## Lacunas funcionais da camada de servicos

- Nao ha service dedicado para migracoes, seeds ou geoprocessamento.
- `RelatorioService` e `PainelService` atuam como fachadas leves sobre repository.
- Auditoria de exportacao CSV e impressao de relatorio ainda nao foi implementada.
