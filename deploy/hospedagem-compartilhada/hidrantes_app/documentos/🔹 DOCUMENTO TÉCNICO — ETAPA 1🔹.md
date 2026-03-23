# Documento Tecnico - Etapa 1

## Situacao do entregavel

Este documento registra a base funcional entregue e estabilizada ate **22/03/2026** para o Sistema de Gestao de Hidrantes. A etapa 1 consolidou a aplicacao web operando em arquitetura MVC customizada com camada de servicos, persistencia em MySQL/MariaDB e interface responsiva orientada a operacao institucional.

## Objetivos atendidos na etapa

- Estruturar o bootstrap da aplicacao em PHP puro, sem framework externo.
- Implementar autenticacao por matricula funcional e senha hash.
- Implantar controle de acesso por perfil (`admin`, `gestor`, `operador`).
- Disponibilizar painel operacional com metricas e mapa interativo.
- Entregar cadastro, edicao, listagem, filtro e exclusao logica de hidrantes.
- Entregar administracao de usuarios para perfil `admin`.
- Entregar historico de auditoria para operacoes de autenticacao e manutencao de cadastros.
- Entregar relatorio tecnico filtravel e exportacao CSV de hidrantes.
- Entregar cadastro e edicao asincrona de bairros vinculados a municipio.

## Componentes tecnicos entregues

### Backend

- `public/index.php` como ponto de entrada unico.
- Autoload interno baseado em `spl_autoload_register`.
- `Router` com resolucao de rotas por padrao e execucao de middlewares.
- `Request`, `Response`, `Session`, `View` e `Database` como nucleo de infraestrutura.
- Controllers especializados por dominio.
- Services para regra de negocio.
- Repositories com SQL parametrizado via `PDO`.

### Banco de dados

- Schema centralizado em `database/schema/schema.sql`.
- Tabelas: `usuarios`, `municipios`, `bairros`, `hidrantes`, `historico_usuario`.
- Exclusao logica somente para `hidrantes`.
- Usuario administrativo inicial semeado no schema.

### Frontend

- Layout autenticado com `sidebar`, `topbar` e areas de conteudo reutilizaveis.
- Layout de autenticacao com painel operacional publico na tela de login.
- CSS modular em `resources/assets/css`.
- JavaScript por pagina em `resources/assets/js/pages`.
- Consumo assicrono de endpoints internos com `fetch`.

## Casos de uso efetivamente entregues

### Acesso

- Login com validacao de credenciais e usuario ativo.
- Logout com destruicao de sessao.
- Protecao contra repeticao de tentativas de login por janela temporal.

### Operacao de hidrantes

- Cadastro de hidrante com validacao server-side.
- Edicao de hidrante por `admin`, `gestor` e `operador`.
- Restricao para que `operador` nao altere `numero_hidrante`.
- Upload de ate 3 imagens por hidrante.
- Georreferenciamento por latitude e longitude.
- Filtros por texto, status, municipio e bairro.
- Exportacao CSV com saneamento contra formula injection.

### Governanca e apoio

- Cadastro, edicao, alteracao de senha e ativacao/inativacao de usuarios.
- Consulta do historico de operacoes.
- Relatorio tecnico de hidrantes com pre-visualizacao para impressao.
- Cadastro e edicao de bairros sem recarga completa da pagina.

## Itens ainda fora do escopo implementado

- Recuperacao de senha por e-mail.
- Migracoes versionadas de banco de dados.
- Suite automatizada de testes.
- API publica para terceiros.
- Seed automatizado de municipios e bairros.
- Processamento efetivo do campo `geojson_referencia`.

## Resultado arquitetural da etapa

Ao final desta etapa, o sistema encontra-se apto para operacao assistida em ambiente web institucional, com separacao clara entre apresentacao, controle, servicos e persistencia. A base entregue ja suporta manutencao evolutiva, mas ainda depende de ampliacoes em automacao de testes, governanca de deploy e carga referencial de dados territoriais.
