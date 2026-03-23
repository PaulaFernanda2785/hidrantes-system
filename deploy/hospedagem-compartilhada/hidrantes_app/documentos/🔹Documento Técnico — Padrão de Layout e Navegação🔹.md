# Documento Tecnico - Padrao de Layout e Navegacao

## Estrutura de interface

O frontend adota dois layouts principais:

- `layouts/auth`: tela de autenticacao com painel operacional publico.
- `layouts/master`: area autenticada com `sidebar`, `topbar` e conteudo principal.

## Padrao visual

Elementos recorrentes:

- cards para organizacao de blocos;
- area hero no topo das paginas;
- grades de metricas;
- tabelas responsivas;
- formularios em grid;
- modais e drawers para detalhe rapido;
- feedback por mensagens flash.

## Navegacao autenticada

Menu lateral atual:

- `Painel`
- `Hidrantes`
- `Usuarios` apenas para `admin`
- `Relatorios` e `Historico` para `admin` e `gestor`

O estado ativo do menu e calculado no layout com base na URI atual.

## Comportamento responsivo

- `sidebar` vira menu colapsavel em telas menores;
- `core/app.js` controla abertura, fechamento e backdrop;
- o layout usa CSS dedicado de responsividade em `layouts/responsive.css`.

## Padrao por pagina

### Login

- bloco institucional;
- formulario de autenticacao;
- painel operacional visivel sem login.

### Painel

- metricas operacionais;
- mapa interativo;
- legenda de status;
- lista de hidrantes proximos;
- drawer lateral de detalhe.

### Hidrantes

- hero com contadores;
- filtros no topo;
- tabela paginada;
- modal de detalhe;
- formulario unico para cadastro e edicao.

### Usuarios

- hero com metricas por perfil;
- tabela com acoes administrativas;
- formulario separado para cadastro/edicao;
- tela dedicada para troca de senha.

### Relatorios

- filtros equivalentes aos da listagem de hidrantes;
- tabela resumo;
- modal de pre-visualizacao para impressao.

### Historico

- filtros por usuario e acao;
- tabela paginada;
- modal de detalhe de auditoria.

## Padrao de interacao

- `GET` para filtros e navegacao.
- `POST` para operacoes de escrita e logout.
- `fetch` para bairros, modais tecnicos e interacoes assicronas.
- confirmacoes do navegador para exclusao e mudanca de status.

## Consideracoes de UX relevantes

- o painel operacional publico na tela de login reduz friccao para consulta visual, mas amplia a superficie de exposicao informacional;
- o formulario de hidrantes foi pensado para uso em desktop e celular, com suporte a camera e geolocalizacao;
- a navegacao e orientada a perfil, mas a protecao real ocorre no backend.
