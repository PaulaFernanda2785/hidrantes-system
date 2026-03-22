# Documento Tecnico - Tabelas

## Tabelas persistidas no schema atual

## `usuarios`

Finalidade:

- armazenar identidade, credencial e permissao de acesso.

Consumidores principais:

- `AuthService`
- `UsuarioService`
- `UsuarioRepository`

Observacoes:

- nao ha exclusao logica;
- o controle de uso e feito por `status`.

## `municipios`

Finalidade:

- armazenar referencia territorial primaria do cadastro de hidrantes.

Consumidores principais:

- `MunicipioRepository`
- `HidranteController`
- `RelatorioController`

Observacoes:

- o schema nao inclui seed de municipios;
- somente municipios `ativo = 1` sao listados pela aplicacao.

## `bairros`

Finalidade:

- armazenar subdivisao territorial dependente de municipio.

Consumidores principais:

- `BairroRepository`
- `BairroService`
- `HidranteService`

Observacoes:

- admite reativacao por `ativo = 1`;
- possui campo `geojson_referencia` ainda sem uso operacional.

## `hidrantes`

Finalidade:

- armazenar o cadastro tecnico e operacional do hidrante.

Consumidores principais:

- `HidranteRepository`
- `HidranteService`
- `PainelService`
- `RelatorioService`

Observacoes:

- contem dados de estado fisico, teste, localizacao e imagens;
- usa exclusao logica por `deleted_at`;
- preserva autoria de criacao, atualizacao e exclusao.

## `historico_usuario`

Finalidade:

- armazenar a trilha de auditoria do sistema.

Consumidores principais:

- `AuditService`
- `HistoricoRepository`
- `HistoricoService`

Observacoes:

- registra evento, entidade, referencia e detalhes;
- contem snapshot do nome do usuario para leitura historica.
