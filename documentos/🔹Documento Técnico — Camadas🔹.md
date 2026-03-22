# Documento Tecnico - Camadas

## Estrutura em camadas adotada

O sistema foi implementado com separacao explicita entre camadas tecnicas e de negocio. A distribuicao atual e a seguinte:

1. Bootstrap e infraestrutura base.
2. Middleware HTTP.
3. Controllers.
4. Services.
5. Repositories.
6. Views e assets.
7. Persistencia relacional e armazenamento de arquivos.

## 1. Bootstrap e infraestrutura base

Local principal:

- `public/index.php`
- `app/Core`
- `app/Helpers/functions.php`
- `config`

Responsabilidades:

- carregar ambiente (`.env`);
- iniciar sessao;
- registrar tratamento de erros;
- enviar headers de seguranca;
- instanciar router e registrar rotas;
- prover abstrações de requisicao, resposta, view, banco e sessao.

Nao contem regra de negocio de hidrantes ou usuarios.

## 2. Middleware HTTP

Classes:

- `AuthMiddleware`
- `GuestMiddleware`
- `RoleMiddleware`

Responsabilidades:

- validar sessao autenticada;
- impedir acesso indevido a rotas privadas;
- impedir acesso de usuario autenticado a fluxo guest;
- aplicar autorizacao por perfil.

Observacao: a validacao de CSRF nao esta em um middleware dedicado; ela e executada centralmente no `Router` para metodos de escrita.

## 3. Controllers

Os controllers recebem parametros de rota e formulario, chamam os services e retornam `view`, `json`, `redirect` ou download de arquivo.

Classes principais:

- `AuthController`
- `PainelController`
- `HidranteController`
- `UsuarioController`
- `RelatorioController`
- `HistoricoController`
- `AssetController`

Controllers nao executam SQL diretamente.

## 4. Services

A camada de servicos concentra a regra de negocio efetiva.

Classes principais:

- `AuthService`
- `LoginThrottleService`
- `PainelService`
- `HidranteService`
- `UsuarioService`
- `BairroService`
- `RelatorioService`
- `HistoricoService`
- `AuditService`
- `UploadService`
- `GeoService`
- `PasswordService`

Responsabilidades:

- validar dominio;
- normalizar entrada;
- aplicar restricoes por perfil;
- processar upload;
- registrar auditoria;
- orquestrar acesso aos repositories.

## 5. Repositories

Classes:

- `UsuarioRepository`
- `MunicipioRepository`
- `BairroRepository`
- `HidranteRepository`
- `HistoricoRepository`

Responsabilidades:

- encapsular SQL;
- aplicar filtros;
- executar paginacao;
- consultar agregacoes;
- persistir entidades e historico.

Nao ha ORM nem camada de model ativa entre service e banco.

## 6. Views e assets

Locais:

- `resources/views`
- `resources/assets/css`
- `resources/assets/js`

Padrao atual:

- renderizacao server-side com PHP;
- layouts separados para area autenticada e tela de login;
- CSS modular por escopo;
- JavaScript por pagina para interacoes assicronas especificas.

## 7. Persistencia relacional e arquivos

Locais:

- `database/schema/schema.sql`
- `storage/uploads/hidrantes`
- `storage/framework`

Responsabilidades:

- armazenar dados normalizados do dominio;
- manter trilha de auditoria;
- suportar exclusao logica de hidrantes;
- armazenar imagens fora da pasta publica principal;
- persistir o estado do limitador de tentativas de login.

## Dependencias entre camadas

As dependencias seguem majoritariamente o sentido abaixo:

`Controller -> Service -> Repository -> Database`

Camadas transversais:

- middleware protege controllers;
- helpers apoiam todas as camadas;
- sessao e configuracao podem ser lidas por middleware, controllers e services;
- views recebem apenas dados ja preparados.

## Restricoes de desenho observadas no codigo

- A validacao de dominio esta majoritariamente nos services.
- `ValidationException` padroniza erros de entrada.
- A interface pode ocultar acoes, mas a protecao efetiva esta nas rotas e middlewares.
- O projeto possui classe `ApiController`, porem as rotas API ativas utilizam `HidranteController`.
