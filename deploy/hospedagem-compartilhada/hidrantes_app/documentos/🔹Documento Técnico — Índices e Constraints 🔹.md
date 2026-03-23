# Documento Tecnico - Indices e Constraints

## Constraints de unicidade

| Tabela | Constraint | Coluna(s) | Finalidade |
| --- | --- | --- | --- |
| `usuarios` | `uq_usuarios_matricula` | `matricula_funcional` | Impedir duplicidade de login. |
| `municipios` | `uq_municipios_nome` | `nome` | Evitar municipios duplicados. |
| `municipios` | `uq_municipios_codigo_ibge` | `codigo_ibge` | Preservar referencia externa unica. |
| `bairros` | `uq_bairros_municipio_nome` | `municipio_id, nome` | Evitar bairro duplicado dentro do mesmo municipio. |
| `bairros` | `uq_bairros_codigo_ibge` | `codigo_ibge` | Preservar referencia externa unica. |
| `hidrantes` | `uq_hidrantes_numero` | `numero_hidrante` | Impedir cadastro duplicado de hidrante. |

## Constraints `CHECK`

Declaradas em `hidrantes`:

- `chk_hidrantes_latitude`: latitude entre `-90` e `90`.
- `chk_hidrantes_longitude`: longitude entre `-180` e `180`.
- `chk_hidrantes_resultado_teste`: quando `teste_realizado = nao`, `resultado_teste` deve ser `NULL`.

## Indices de pesquisa e filtro

### `usuarios`

- `idx_usuarios_nome`
- `idx_usuarios_perfil`
- `idx_usuarios_status`

Motivacao:

- suportar listagem por nome e analises por perfil/status.

### `municipios`

- `idx_municipios_ativo`

Motivacao:

- carregar apenas municipios ativos.

### `bairros`

- `idx_bairros_nome`
- `idx_bairros_ativo`

Motivacao:

- ordenacao e carga de bairros ativos.

### `hidrantes`

- `idx_hidrantes_status_operacional`
- `idx_hidrantes_municipio_id`
- `idx_hidrantes_bairro_id`
- `idx_hidrantes_tipo_hidrante`
- `idx_hidrantes_area`
- `idx_hidrantes_atualizado_em`
- `idx_hidrantes_municipio_bairro`
- `idx_hidrantes_status_municipio`
- `idx_hidrantes_deleted_at`

Motivacao:

- suportar filtros operacionais;
- acelerar listagens por atualizacao;
- separar registros ativos dos excluidos logicamente.

### `historico_usuario`

- `idx_historico_usuario_usuario_id`
- `idx_historico_usuario_acao`
- `idx_historico_usuario_data_acao`
- `idx_historico_usuario_usuario_acao`

Motivacao:

- suportar filtros por usuario, acao e ordenacao temporal.

## Observacoes de uso

- A aplicacao usa `LIKE` em texto livre para alguns filtros de hidrantes e usuarios; os indices ajudam parcialmente, mas buscas por substring continuam tendo custo maior.
- A regra de unicidade de `numero_hidrante` permanece efetiva mesmo apos exclusao logica, pois o registro continua presente na tabela.
- Os `CHECK` existem no schema e devem ser interpretados em conjunto com as validacoes server-side nos services.
