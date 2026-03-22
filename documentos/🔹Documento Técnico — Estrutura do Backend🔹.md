# Documento Tecnico - Estrutura do Backend

## Panorama

O backend atual e um monolito PHP com roteamento proprio, `PDO` para acesso a dados e renderizacao server-side. A organizacao privilegia simplicidade operacional, baixo numero de dependencias e separacao manual de responsabilidades.

## Ponto de entrada

Arquivo principal:

- `public/index.php`

Etapas executadas:

1. definicao de `BASE_PATH`;
2. configuracao de timezone;
3. registro do autoload interno;
4. carga de helpers globais;
5. leitura do `.env`;
6. abertura da sessao;
7. registro de tratamento de erro;
8. envio de headers de seguranca;
9. carga de `routes/web.php` e `routes/api.php`;
10. despacho da requisicao para o `Router`.

## Nucleo tecnico

Classes de infraestrutura:

- `Env`: leitura do arquivo `.env`.
- `Request`: encapsula metodo HTTP, URI, entrada e arquivos.
- `Response`: resposta JSON.
- `Router`: registro de rotas, resolucao por padrao, middleware e CSRF.
- `Session`: gestao de sessao, regeneracao e flash messages.
- `View`: renderizacao de templates com layout.
- `Database`: singleton de conexao `PDO`.
- `Controller`: classe base para controllers.

## Organizacao funcional

### Autenticacao e sessao

- `AuthController`
- `AuthService`
- `LoginThrottleService`
- `PasswordService`
- `AuthMiddleware`
- `GuestMiddleware`
- `RoleMiddleware`

### Hidrantes e territorial

- `HidranteController`
- `HidranteService`
- `BairroService`
- `UploadService`
- `GeoService`
- `HidranteRepository`
- `BairroRepository`
- `MunicipioRepository`

### Governanca e operacao

- `PainelController`
- `RelatorioController`
- `HistoricoController`
- `PainelService`
- `RelatorioService`
- `HistoricoService`
- `AuditService`
- `HistoricoRepository`

### Administracao de usuarios

- `UsuarioController`
- `UsuarioService`
- `UsuarioRepository`

## Estrategia de persistencia

- Sem ORM.
- Queries SQL escritas manualmente em repositories.
- Sempre com `prepare/execute` para parametros de entrada.
- Paginacao implementada por `LIMIT/OFFSET`.
- Agregacoes de painel calculadas diretamente em SQL.

## Estrategia de seguranca

- senha armazenada com `password_hash`;
- validacao de sessao com fingerprint por user-agent;
- timeout por inatividade baseado em `session.lifetime`;
- protecao CSRF centralizada no router;
- validacao de upload por MIME, dimensao e tamanho;
- `realpath` e whitelist de extensoes para servir assets;
- validacao de nome e MIME ao servir fotos.

## Estrategia de resposta

O backend hoje produz quatro tipos de resposta:

- HTML renderizado com layout.
- Redirect com mensagem flash.
- JSON para chamadas internas do frontend.
- CSV para exportacao de hidrantes.

Nao ha suporte implementado para:

- API REST publica completa;
- filas assicronas;
- websocket;
- migracoes automatizadas;
- comandos CLI de manutencao.

## Observacoes de implementacao

- O `composer.json` existe, mas o bootstrap em execucao utiliza autoload proprio em vez de `vendor/autoload.php`.
- O sistema trabalha majoritariamente com rotas `GET` e `POST`.
- Endpoints de API internos tambem recebem `POST` com `FormData`, nao JSON cru.
- O backend nao possui suite automatizada de testes na base atual.
