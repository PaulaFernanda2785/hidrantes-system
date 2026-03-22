# Documento Tecnico - Organizacao de Arquivos

## Principio de organizacao

Os arquivos do projeto estao organizados por responsabilidade, e nao por tecnologia isolada. Isso permite localizar rapidamente o ponto correto de manutencao:

- fluxo HTTP em `routes` e `Controllers`;
- regra de negocio em `Services`;
- persistencia em `Repositories`;
- apresentacao em `resources/views`;
- estilos e scripts em `resources/assets`;
- runtime state em `storage`.

## Convencoes observadas

### PHP de aplicacao

- Classes com namespace `App\\...`.
- Um arquivo por classe.
- Nomes de controllers, services e repositories alinhados ao dominio.

Exemplos:

- `HidranteController`
- `HidranteService`
- `HidranteRepository`

### Views

- Separadas por modulo funcional.
- Layouts reutilizaveis em `resources/views/layouts`.
- Componentes reutilizaveis em `resources/views/components`.

Exemplos:

- `resources/views/hidrantes`
- `resources/views/usuarios`
- `resources/views/relatorios`
- `resources/views/historico`

### Assets

- CSS separado em `globals`, `layouts`, `components` e `pages`.
- JavaScript separado em `core` e `pages`.

Essa divisao reflete o nivel de reutilizacao do arquivo:

- global: variaveis, reset, base;
- layout: sidebar, topbar, responsividade;
- component: botoes, tabelas, modais, formularios;
- pages: regras visuais e scripts especificos de cada tela.

## Relacao entre arquivo e fluxo

### Rotas

- `routes/web.php`: mapeia URLs HTML.
- `routes/api.php`: mapeia URLs JSON.

### Controllers

Recebem a requisicao e direcionam o fluxo para service e view.

### Services

Concentram validacao e regras de negocio.

### Repositories

Persistem e consultam dados no banco.

### Views

Montam a saida HTML.

### Assets

Executam interacao de tela, mapa, filtros dinamicos e modais.

## Organizacao de arquivos de dados e runtime

- `database/schema/schema.sql`: definicao estrutural do banco.
- `storage/uploads/hidrantes`: imagens dos hidrantes.
- `storage/framework/security/login_throttle.json`: estado do rate limiting de login.

## Pontos de atencao

- O repositorio possui `public/assets`, mas a estrategia ativa de entrega de CSS e JS usa `resources/assets` via `AssetController`.
- O campo `geojson_referencia` esta no banco, mas nao ha pasta de geodados integrada ao runtime atual.
- Nao ha pasta `tests`, `migrations` ou `commands`, o que indica ausencia de camadas formais para automacao de testes e evolucao incremental do banco.
