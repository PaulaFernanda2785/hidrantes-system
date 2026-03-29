# Sistema de Gestao de Hidrantes

Aplicacao web para cadastro, consulta e acompanhamento operacional de hidrantes, com foco em apoio a equipes tecnicas e administrativas.

## O que e o sistema

Este projeto centraliza informacoes de hidrantes em uma unica plataforma, com:

- painel operacional com metricas e mapa georreferenciado;
- cadastro, consulta, edicao e exclusao logica de hidrantes;
- filtros por status, municipio e bairro;
- upload de ate 3 fotos por hidrante;
- relatorio tecnico filtravel com impressao;
- exportacao CSV;
- trilha de historico para auditoria;
- controle de acesso por perfil (`admin`, `gestor`, `operador`).

## Problematica

Antes de uma solucao centralizada, a gestao de hidrantes tende a enfrentar problemas como:

- dados espalhados em fontes diferentes, sem padrao unico;
- dificuldade para localizar e validar rapidamente um hidrante em campo;
- pouca rastreabilidade sobre quem alterou informacoes criticas;
- limitacao para gerar visao gerencial consolidada por status e territorio.

## Justificativa

O sistema foi desenvolvido para:

- consolidar informacoes tecnicas e operacionais em um unico ambiente;
- aumentar a confiabilidade dos dados para planejamento e resposta;
- reduzir retrabalho com filtros, relatorios e exportacao estruturada;
- melhorar governanca com autenticacao, autorizacao por perfil e historico de acoes.

## Como foi desenvolvido

A aplicacao foi construida como monolito PHP com renderizacao server-side e arquitetura em camadas:

`Request -> Router -> Middleware -> Controller -> Service -> Repository -> MySQL -> View/JSON/CSV`

Principais decisoes tecnicas:

- arquitetura MVC customizada com `Service Layer` e `Repository Layer`;
- persistencia em MySQL/MariaDB via `PDO` e SQL parametrizado;
- rotas web e API interna para mapa e bairros;
- frontend com HTML/CSS/JS organizado por layout e pagina;
- seguranca com hash de senha, sessao, CSRF e controle de permissao por papel.

## Perfis de acesso

| Perfil | Permissoes principais |
| --- | --- |
| `admin` | Painel, hidrantes, usuarios, relatorios e historico |
| `gestor` | Painel, hidrantes, relatorios e historico |
| `operador` | Painel e operacao de hidrantes (nao cria, nao exclui e nao altera numero do hidrante) |

## Estrutura resumida

```txt
app/                 # Core, controllers, services, repositories, middleware
config/              # Configuracoes da aplicacao
database/schema/     # Schema SQL oficial
documentos/          # Documentacao tecnica e manual do usuario
public/              # Ponto de entrada web e assets servidos
resources/           # Views, CSS e JavaScript fonte
routes/              # Rotas web e API
storage/uploads/     # Arquivos enviados (fotos de hidrantes)
```

## Como executar localmente

1. Copie `.env.example` para `.env`.
2. Configure as credenciais de banco no `.env`.
3. Execute `composer dump-autoload`.
4. Importe `database/schema/schema.sql` no MySQL/MariaDB.
5. Configure seu `DocumentRoot` ou `VirtualHost` para `public/`.
6. Acesse o sistema no navegador.

Acesso inicial:

- O schema inclui um usuario administrador inicial para bootstrap.
- Altere matricula e senha imediatamente apos o primeiro acesso.

## Documentacao complementar

- Documentacao tecnica completa: pasta `documentos/`
- Manual de hospedagem: `documentos/MANUAL-HOSPEDAGEM-COMPARTILHADA.md`
- Manual do usuario (HTML): `documentos/manual-do-usuario.html`
- Banco de dados (schema): `database/schema/schema.sql`

## Estado atual e limitacoes

Escopo funcional principal esta entregue e operacional. Pontos ainda nao implementados:

- recuperacao de senha por e-mail;
- suite automatizada de testes;
- migracoes versionadas;
- seed automatico de municipios e bairros.

> Recomendacao: altere a senha inicial do usuario administrador apos a instalacao.
