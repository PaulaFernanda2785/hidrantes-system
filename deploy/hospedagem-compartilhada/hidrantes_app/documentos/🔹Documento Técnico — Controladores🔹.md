# Documento Tecnico - Controladores

## Papel da camada de controllers

Os controllers atuais atuam como camada de orquestracao HTTP. Eles recebem dados da rota ou do formulario, invocam os services ou repositories necessarios e retornam uma resposta adequada ao tipo de caso de uso.

## Controllers implementados

### `AuthController`

Responsabilidades:

- renderizar a tela de login;
- processar autenticacao;
- iniciar controle de tentativas com `LoginThrottleService`;
- encerrar sessao no logout.

Saidas:

- `view` de login;
- `redirect` para `/painel` ou `/login`.

### `PainelController`

Responsabilidades:

- carregar metricas consolidadas;
- carregar pontos georreferenciados;
- renderizar o painel operacional autenticado.

### `HidranteController`

Responsabilidades:

- listar hidrantes com filtros e paginacao;
- abrir formulario de cadastro e edicao;
- delegar criacao, atualizacao e exclusao logica;
- exportar CSV;
- responder JSON para mapa e bairros;
- cadastrar e editar bairros via API interna;
- servir imagens de hidrantes com validacao de nome e MIME.

Observacoes:

- concentra tanto fluxo HTML quanto parte da API interna do modulo;
- existe rota publica de foto para suporte ao painel exibido na tela de login.

### `UsuarioController`

Responsabilidades:

- listar usuarios com filtro por nome;
- abrir formularios de criacao e edicao;
- criar usuario;
- atualizar dados cadastrais;
- abrir tela de troca de senha para outro usuario (`/usuarios/{id}/senha`);
- atualizar senha de outro usuario;
- abrir tela de troca da propria senha (`/minha-senha`);
- atualizar propria senha com validacao da senha atual;
- ativar e inativar usuario.

### `RelatorioController`

Responsabilidades:

- aplicar filtros de hidrantes;
- consultar o `RelatorioService`;
- carregar municipios e bairros para o filtro;
- renderizar a tela de relatorio e pre-visualizacao para impressao.

### `HistoricoController`

Responsabilidades:

- filtrar historico por usuario e acao;
- paginar registros;
- renderizar a listagem com modal de detalhe.

### `AssetController`

Responsabilidades:

- servir CSS e JS de `resources/assets`;
- validar caminho e extensao;
- impedir traversal de diretorio;
- definir `Content-Type` e cache de curta duracao.

### `ApiController`

Responsabilidades implementadas no codigo:

- responder pontos do mapa;
- responder bairros por municipio.

Estado atual:

- a classe existe, mas nao esta conectada nas rotas ativas;
- as rotas API do projeto utilizam `HidranteController`.

## Padrao de implementacao observado

Os controllers seguem o mesmo padrao:

1. capturam parametros de entrada;
2. instanciam service ou repository;
3. tratam excecoes esperadas, principalmente `ValidationException`;
4. retornam `view`, `json`, `redirect` ou arquivo.

## Limites de responsabilidade

Boas praticas efetivamente observadas:

- SQL permanece fora dos controllers.
- Validacoes de negocio mais importantes ficam nos services.
- Mensagens de retorno ao usuario sao centralizadas em `redirect` com flash.

Pontos de atencao:

- `HidranteController` acumula responsabilidades HTML, API e streaming de arquivo.
- Auditoria de exportacao CSV e geracao de relatorio nao esta implementada no controller nem no service correspondente.
