# Documento Tecnico - Dicionario de Dados

## `usuarios`

| Campo | Tipo | Obrigatorio | Descricao |
| --- | --- | --- | --- |
| `id` | `BIGINT UNSIGNED` | Sim | Identificador tecnico. |
| `nome` | `VARCHAR(150)` | Sim | Nome completo do usuario. |
| `matricula_funcional` | `VARCHAR(30)` | Sim | Identificador de login, unico. |
| `senha_hash` | `VARCHAR(255)` | Sim | Hash da senha. |
| `perfil` | `ENUM` | Sim | `admin`, `gestor` ou `operador`. |
| `status` | `ENUM` | Sim | `ativo` ou `inativo`. |
| `criado_em` | `DATETIME` | Sim | Data de criacao. |
| `atualizado_em` | `DATETIME` | Nao | Data da ultima alteracao. |

## `municipios`

| Campo | Tipo | Obrigatorio | Descricao |
| --- | --- | --- | --- |
| `id` | `BIGINT UNSIGNED` | Sim | Identificador tecnico. |
| `nome` | `VARCHAR(150)` | Sim | Nome do municipio. |
| `codigo_ibge` | `VARCHAR(20)` | Nao | Codigo de referencia externa. |
| `uf` | `CHAR(2)` | Sim | Unidade federativa, default `PA`. |
| `ativo` | `TINYINT(1)` | Sim | Indicador de disponibilidade para uso. |

## `bairros`

| Campo | Tipo | Obrigatorio | Descricao |
| --- | --- | --- | --- |
| `id` | `BIGINT UNSIGNED` | Sim | Identificador tecnico. |
| `municipio_id` | `BIGINT UNSIGNED` | Sim | Municipio pai. |
| `nome` | `VARCHAR(150)` | Sim | Nome do bairro. |
| `codigo_ibge` | `VARCHAR(30)` | Nao | Codigo de referencia externa. |
| `geojson_referencia` | `LONGTEXT` | Nao | Campo reservado para conteudo geoespacial. |
| `ativo` | `TINYINT(1)` | Sim | Indicador de disponibilidade para uso. |

## `hidrantes`

| Campo | Tipo | Obrigatorio | Descricao |
| --- | --- | --- | --- |
| `id` | `BIGINT UNSIGNED` | Sim | Identificador tecnico. |
| `numero_hidrante` | `VARCHAR(20)` | Sim | Codigo de identificacao operacional, unico. |
| `equipe_responsavel` | `VARCHAR(150)` | Sim | Equipe que responde pelo registro. |
| `area` | `ENUM` | Sim | `urbano`, `industrial` ou `rural`. |
| `existe_no_local` | `ENUM` | Sim | `sim` ou `nao`. |
| `tipo_hidrante` | `ENUM` | Sim | `coluna`, `subterraneo`, `parede` ou `outro`. |
| `acessibilidade` | `ENUM` | Sim | Condicao de acesso fisico. |
| `tampo_conexoes` | `ENUM` | Sim | Estado geral de tampo e conexoes. |
| `tampas_ausentes` | `VARCHAR(100)` | Nao | Texto livre para detalhar ausencia de tampas. |
| `caixa_protecao` | `ENUM` | Sim | Existencia de caixa de protecao. |
| `condicao_caixa` | `ENUM` | Nao | `boa`, `regular` ou `ruim`. |
| `presenca_agua_interior` | `ENUM` | Sim | Indicador de presenca de agua. |
| `teste_realizado` | `ENUM` | Sim | `sim` ou `nao`. |
| `resultado_teste` | `ENUM` | Nao | Resultado do teste funcional. |
| `status_operacional` | `ENUM` | Sim | Estado operacional consolidado. |
| `municipio_id` | `BIGINT UNSIGNED` | Sim | Municipio do hidrante. |
| `bairro_id` | `BIGINT UNSIGNED` | Nao | Bairro do hidrante. |
| `endereco` | `VARCHAR(255)` | Sim | Endereco textual. |
| `latitude` | `DECIMAL(10,7)` | Nao | Latitude georreferenciada. |
| `longitude` | `DECIMAL(10,7)` | Nao | Longitude georreferenciada. |
| `foto_01` | `VARCHAR(255)` | Nao | Nome do primeiro arquivo de imagem. |
| `foto_02` | `VARCHAR(255)` | Nao | Nome do segundo arquivo de imagem. |
| `foto_03` | `VARCHAR(255)` | Nao | Nome do terceiro arquivo de imagem. |
| `criado_em` | `DATETIME` | Sim | Data de criacao. |
| `atualizado_em` | `DATETIME` | Sim | Data da ultima alteracao. |
| `criado_por_usuario_id` | `BIGINT UNSIGNED` | Nao | Usuario que criou. |
| `atualizado_por_usuario_id` | `BIGINT UNSIGNED` | Nao | Usuario que atualizou por ultimo. |
| `deleted_at` | `DATETIME` | Nao | Marcador de exclusao logica. |
| `deleted_por_usuario_id` | `BIGINT UNSIGNED` | Nao | Usuario que executou a exclusao logica. |

## `historico_usuario`

| Campo | Tipo | Obrigatorio | Descricao |
| --- | --- | --- | --- |
| `id` | `BIGINT UNSIGNED` | Sim | Identificador tecnico. |
| `data_acao` | `DATETIME` | Sim | Data e hora do evento. |
| `usuario_id` | `BIGINT UNSIGNED` | Sim | Usuario executor. |
| `usuario_nome_snapshot` | `VARCHAR(150)` | Sim | Nome do usuario no momento da acao. |
| `acao` | `ENUM` | Sim | Tipo de operacao auditada. |
| `entidade` | `VARCHAR(50)` | Nao | Entidade afetada (`usuarios`, `hidrantes`, `bairros`, etc.). |
| `referencia_registro` | `VARCHAR(100)` | Nao | Identificador textual do registro relacionado. |
| `detalhes` | `TEXT` | Nao | Descricao complementar da operacao. |
| `ip_origem` | `VARCHAR(45)` | Nao | IP de origem da requisicao. |
