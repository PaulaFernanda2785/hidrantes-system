# Documento Tecnico - Arquitetura de Banco de Dados

## Plataforma de persistencia

O sistema utiliza **MySQL/MariaDB** com engine `InnoDB`, charset `utf8mb4` e collation `utf8mb4_unicode_ci`. A definicao estrutural atual encontra-se centralizada em:

- `database/schema/schema.sql`

## Estrategia de modelagem

O banco foi desenhado em torno de cinco entidades persistidas:

- `usuarios`
- `municipios`
- `bairros`
- `hidrantes`
- `historico_usuario`

Essa composicao atende tres eixos:

1. seguranca e acesso;
2. base territorial;
3. operacao e auditoria.

## Organizacao logica

### Dominio de acesso

- `usuarios`: identidade, credencial e perfil.
- `historico_usuario`: rastreabilidade de operacoes.

### Dominio territorial

- `municipios`: referencia administrativa.
- `bairros`: subdivisao territorial vinculada a municipio.

### Dominio operacional

- `hidrantes`: cadastro tecnico, localizacao, imagens, status operacional e rastreio de autoria.

## Padrao de integridade

O banco usa:

- chaves primarias `BIGINT UNSIGNED AUTO_INCREMENT`;
- chaves estrangeiras com `ON UPDATE CASCADE`;
- `UNIQUE` para matricula, numero de hidrante e combinacoes territoriais;
- `CHECK` para coordenadas e consistencia do resultado de teste;
- indices compostos e simples para filtros e listagens.

## Estrategia de ciclo de vida dos dados

### Usuarios

- permanecem fisicamente no banco;
- variacao de acesso ocorre por `status` (`ativo`/`inativo`).

### Bairros e municipios

- municipios usam flag `ativo`;
- bairros usam flag `ativo`;
- nao ha exclusao logica com `deleted_at`.

### Hidrantes

- utilizam exclusao logica por `deleted_at`;
- o registro continua no banco apos exclusao funcional;
- `deleted_por_usuario_id` guarda o ator da remocao.

## Seeds e carga inicial

O schema entrega apenas um seed obrigatorio:

- usuario administrador inicial (credenciais definidas internamente)

Observacoes:

- nao ha seed nativo de municipios;
- nao ha seed nativo de bairros;
- a carga territorial depende de procedimento externo ou cadastro operacional.

## Aspectos arquiteturais relevantes

- Nao ha migracoes incrementais; a evolucao do banco hoje depende de manutencao do arquivo SQL unico.
- Nao ha procedures, triggers ou views definidas no schema atual.
- O banco foi desenhado para consumo direto por repositories com SQL explicito.
- O campo `geojson_referencia` em `bairros` antecipa uma futura evolucao geoespacial, mas nao participa do fluxo funcional implementado hoje.
