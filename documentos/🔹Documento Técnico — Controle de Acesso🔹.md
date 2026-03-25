# Documento Tecnico - Controle de Acesso

## Modelo de acesso

O sistema combina tres mecanismos de protecao:

1. autenticacao por sessao;
2. autorizacao por perfil;
3. validacao de contexto da sessao.

Perfis previstos no banco e no codigo:

- `admin`
- `gestor`
- `operador`

## Autenticacao

Implementacao atual:

- login por `matricula_funcional` e senha;
- validacao de `status = ativo`;
- armazenamento do contexto autenticado em `Session::put('auth', ...)`;
- regeneracao de sessao apos login;
- limpeza do token CSRF anterior.

## Integridade da sessao

`AuthMiddleware` valida:

- existencia de `auth` em sessao;
- fingerprint baseada em `user-agent`;
- tempo maximo de inatividade conforme `session.lifetime`.

Se houver inconsistencias:

- a sessao e invalidada;
- o usuario retorna ao login com mensagem de seguranca.

## Controle de tentativas de login

Politica implementada:

- 5 tentativas falhas por janela de 15 minutos;
- bloqueio de 15 minutos;
- controle por IP e por combinacao matricula+IP.

## Matriz de autorizacao por rota

### Rotas publicas

- `GET /assets/...`
- `GET /login`
- `POST /login`
- `GET /painel/fotos/hidrantes/{filename}`

### Rotas para qualquer usuario autenticado (`admin`, `gestor`, `operador`)

- `POST /logout`
- `GET /`
- `GET /painel`
- `GET /minha-senha`
- `POST /minha-senha`
- `GET /hidrantes`
- `GET /hidrantes/exportar/csv`
- `GET /uploads/hidrantes/{filename}`
- `GET /hidrantes/{id}/editar`
- `POST /hidrantes/{id}/atualizar`
- `GET /api/hidrantes/mapa`
- `GET /api/bairros/municipio/{id}`
- `POST /api/bairros`
- `POST /api/bairros/{id}`

### Rotas restritas a `admin` e `gestor`

- `GET /hidrantes/novo`
- `POST /hidrantes/salvar`
- `POST /hidrantes/{id}/excluir`
- `GET /relatorios/hidrantes`
- `GET /historico`

### Rotas restritas a `admin`

- `GET /usuarios`
- `GET /usuarios/novo`
- `POST /usuarios/salvar`
- `GET /usuarios/{id}/editar`
- `POST /usuarios/{id}/atualizar`
- `GET /usuarios/{id}/senha`
- `POST /usuarios/{id}/senha`
- `POST /usuarios/{id}/status`

## Regras especificas de autorizacao de negocio

- `operador` pode editar hidrante existente, mas nao pode alterar `numero_hidrante`.
- `operador` nao pode criar nem excluir hidrantes.
- `operador` tambem nao acessa usuarios, historico e relatorios.
- qualquer usuario autenticado pode alterar a propria senha em `/minha-senha`.
- alteracao da propria senha exige confirmacao da `senha_atual`.
- `admin` usa `/usuarios/{id}/senha` apenas para outros usuarios.
- qualquer usuario autenticado pode cadastrar ou editar bairro pelas rotas API internas.
- apenas `admin` gerencia usuarios.

## Protecoes complementares

- CSRF obrigatorio em formularios `POST`.
- escape de saida HTML em views.
- validacao de upload e entrega controlada de arquivos.
- pagina `403` para negacao de acesso por perfil.

## Ponto de atencao arquitetural

Embora menus ocultem opcoes por perfil, a protecao efetiva esta no backend. Isso esta corretamente refletido no uso combinado de middlewares e regras de negocio em service.
