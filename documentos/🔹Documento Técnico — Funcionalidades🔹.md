# Documento Tecnico - Funcionalidades

## Escopo funcional implementado

O sistema opera hoje com sete blocos funcionais principais:

1. autenticacao e sessao;
2. painel operacional;
3. gestao de hidrantes;
4. gestao de usuarios;
5. base territorial;
6. relatorios;
7. historico de operacoes.

## 1. Autenticacao e sessao

Funcionalidades entregues:

- login por matricula funcional e senha;
- logout;
- bloqueio de usuario inativo;
- limitacao de tentativas sucessivas de login;
- redirecionamento automatico de usuario ja autenticado;
- expiracao de sessao por inatividade.

Nao implementado:

- recuperacao de senha;
- redefinicao por e-mail;
- MFA.

## 2. Painel operacional

Funcionalidades entregues:

- total de hidrantes;
- total de operantes;
- total de operantes com restricao;
- total de inoperantes;
- mapa interativo com pontos georreferenciados;
- troca de camadas do mapa;
- captura de localizacao do usuario;
- listagem dos hidrantes mais proximos;
- drawer com ficha completa do hidrante;
- acesso por rota autenticada `/painel`.

Observacao:

- o mesmo painel, em versao publica, tambem e exibido na tela de login com dados renderizados no servidor.

## 3. Gestao de hidrantes

Funcionalidades entregues:

- listagem paginada;
- filtro por texto;
- filtro por status operacional;
- filtro por municipio;
- filtro por bairro;
- cadastro;
- edicao;
- exclusao logica;
- upload de ate 3 fotos;
- anexo por selecao de arquivo, camera e drag and drop;
- validacao de coordenadas;
- visualizacao detalhada em modal;
- exportacao CSV.

Regras de negocio relevantes:

- `numero_hidrante` deve ser unico;
- `bairro_id` deve pertencer ao `municipio_id` informado;
- `operador` pode editar hidrante, mas nao pode alterar `numero_hidrante`;
- exclusao remove o registro das consultas funcionais por `deleted_at`.

## 4. Gestao de usuarios

Funcionalidades entregues:

- listagem paginada;
- filtro por nome;
- cadastro;
- edicao;
- alteracao de senha;
- ativacao e inativacao.

Regras de negocio relevantes:

- somente `admin` acessa o modulo;
- matricula funcional deve ser unica;
- senha minima exige 8 caracteres com letras e numeros;
- usuario nao pode alterar o proprio perfil/status pela tela administrativa;
- usuario nao pode inativar a propria conta pela tela administrativa.

## 5. Base territorial

Funcionalidades entregues:

- listagem de municipios ativos;
- listagem de bairros ativos por municipio;
- cadastro assicrono de bairro;
- edicao assicrona de bairro;
- reativacao automatica de bairro inativo ja existente.

Observacoes:

- `municipio_id` e obrigatorio para cadastro de hidrante;
- o sistema nao entrega seed automatizado de municipios e bairros no schema atual.

## 6. Relatorios

Funcionalidades entregues:

- filtro de hidrantes por texto, status, municipio e bairro;
- relatorio em tela;
- pre-visualizacao estruturada para impressao;
- fichas tecnicas individuais;
- paginas fotograficas quando houver imagens anexadas.

Nao implementado:

- exportacao PDF nativa;
- auditoria de emissao de relatorio;
- agendamento de relatorios.

## 7. Historico de operacoes

Funcionalidades entregues:

- listagem paginada;
- filtro por nome do usuario;
- filtro por acao;
- detalhe em modal;
- resolucao contextual de referencia para hidrante, usuario e bairro.

Eventos hoje auditados no codigo:

- login;
- logout;
- cadastrar usuario;
- editar usuario;
- alterar senha;
- ativar usuario;
- inativar usuario;
- cadastrar hidrante;
- editar hidrante;
- deletar hidrante;
- cadastrar bairro;
- editar bairro;
- reativar bairro.
