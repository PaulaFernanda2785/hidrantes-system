# Documento Tecnico - Arquitetura do Projeto

## Visao geral

O sistema foi implementado como uma aplicacao web monolitica em PHP, com arquitetura **MVC customizada expandida por Service Layer e Repository Layer**. O objetivo arquitetural atual e manter baixo acoplamento entre fluxo HTTP, regras de negocio, persistencia e interface, sem dependencia de framework full stack.

## Estilo arquitetural

O fluxo principal de execucao e:

`HTTP Request -> Router -> Middleware -> Controller -> Service -> Repository -> PDO/MySQL -> View/JSON/Redirect`

Essa composicao foi adotada porque o dominio possui:

- autenticacao e autorizacao por perfil;
- validacoes de negocio para hidrantes e usuarios;
- auditoria transacional;
- upload de imagens;
- renderizacao server-side com interacoes assicronas pontuais;
- consultas agregadas para painel, relatorios e historico.

## Componentes arquiteturais

### Nucleo de aplicacao

- `public/index.php`: bootstrap, carga de ambiente, sessao, headers de seguranca e despacho de rotas.
- `app/Core`: abstrai roteamento, requisicao, resposta, view, sessao, banco e configuracao.
- `app/Helpers/functions.php`: funcoes utilitarias globais para configuracao, seguranca, URL, CSRF e tratamento de erros.

### Camada HTTP

- `routes/web.php`: rotas HTML e operacoes de formulario.
- `routes/api.php`: rotas JSON internas para mapa e bairros.
- `app/Middleware`: autenticacao, fluxo guest e autorizacao por perfil.

### Camada de dominio

- `app/Controllers`: coordenacao de casos de uso.
- `app/Services`: regras de negocio, validacoes, auditoria, seguranca complementar e processamento de uploads.
- `app/Repositories`: acesso a dados com SQL parametrizado.

### Camada de apresentacao

- `resources/views`: templates PHP e componentes.
- `resources/assets/css` e `resources/assets/js`: estilos e comportamentos por layout e por pagina.
- `AssetController`: entrega de assets diretamente de `resources/assets`, com validacao de caminho.

### Camada de dados

- MySQL/MariaDB com `InnoDB` e `utf8mb4`.
- `database/schema/schema.sql` como definicao unica do banco.
- `storage/uploads/hidrantes` para imagens.
- `storage/framework/security/login_throttle.json` para estado do limitador de tentativas de login.

## Modulos de negocio implementados

- Autenticacao e sessao.
- Painel operacional.
- Gestao de hidrantes.
- Gestao de usuarios.
- Relatorios de hidrantes.
- Historico de auditoria.
- Base territorial de municipios e bairros.

## Decisoes arquiteturais relevantes

### Framework interno leve

O projeto utiliza um nucleo proprio em vez de framework externo. Isso reduz dependencia de terceiros, mas transfere para o proprio codigo a responsabilidade por seguranca, roteamento e tratamento de erros.

### Renderizacao server-side

As paginas principais sao renderizadas no servidor. O JavaScript e usado para:

- mapa interativo;
- geolocalizacao do navegador;
- carga dinamica de bairros;
- modais de detalhe;
- pre-visualizacao de relatorio para impressao;
- interacoes de upload.

### Persistencia sem ORM

Nao ha ORM. Toda persistencia ocorre via `PDO` com SQL explicito em repositories. Isso favorece previsibilidade e controle de consulta, especialmente para filtros, paginação e agregacoes.

### Auditoria orientada a servicos

O registro em `historico_usuario` e acionado principalmente a partir da camada de servicos, preservando a regra de negocio fora dos controllers.

## Limitacoes arquiteturais atuais

- Nao existe camada formal de testes automatizados.
- Nao ha migracoes incrementais; o banco depende de um schema unico.
- Nao ha cache de aplicacao nem fila assicrona.
- O campo `geojson_referencia` existe no banco, mas ainda nao integra um modulo geoespacial.
- A tela de login publica metricas e mapa operacional, o que deve ser considerado na politica institucional de exposicao de informacao.
