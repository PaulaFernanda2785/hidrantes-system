# Documento Tecnico - Requisitos Funcionais

## Escopo

Os requisitos abaixo descrevem o comportamento funcional efetivamente implementado no sistema ate **25/03/2026**.

## Requisitos de acesso

### RF-01 - Autenticar usuario

O sistema deve permitir login por `matricula_funcional` e senha.

### RF-02 - Encerrar sessao

O sistema deve permitir logout e invalidar a sessao ativa.

### RF-03 - Restringir acesso por perfil

O sistema deve aplicar autorizacao por perfil em rotas e operacoes.

### RF-04 - Bloquear tentativas excessivas de login

O sistema deve bloquear temporariamente novas tentativas apos repetidas falhas de autenticacao.

## Requisitos de painel operacional

### RF-05 - Exibir metricas consolidadas de hidrantes

O sistema deve apresentar total geral e totais por status operacional.

### RF-06 - Exibir mapa operacional de hidrantes

O sistema deve apresentar os hidrantes georreferenciados em mapa interativo.

### RF-07 - Exibir detalhe tecnico do hidrante no painel

Ao selecionar um ponto do mapa, o sistema deve exibir ficha detalhada, fotos e link de rota.

### RF-08 - Calcular hidrantes mais proximos

O sistema deve usar a localizacao do navegador para listar os hidrantes mais proximos.

## Requisitos de gestao de hidrantes

### RF-09 - Listar hidrantes

O sistema deve listar hidrantes com paginacao.

### RF-10 - Filtrar hidrantes

O sistema deve filtrar hidrantes por:

- texto livre;
- status operacional;
- municipio;
- bairro.

### RF-11 - Cadastrar hidrante

O sistema deve permitir cadastro de hidrante para perfis `admin` e `gestor`.

### RF-12 - Editar hidrante

O sistema deve permitir edicao de hidrante para perfis `admin`, `gestor` e `operador`.

Regra complementar:

- `operador` nao pode alterar `numero_hidrante`.

### RF-13 - Excluir hidrante logicamente

O sistema deve permitir exclusao logica de hidrante para perfis `admin` e `gestor`.

### RF-14 - Anexar fotos ao hidrante

O sistema deve permitir anexar ate 3 imagens por hidrante.

### RF-15 - Georreferenciar hidrante

O sistema deve permitir armazenar latitude e longitude validas para o hidrante.

### RF-16 - Exportar hidrantes em CSV

O sistema deve permitir exportar a listagem filtrada em arquivo CSV.

## Requisitos de base territorial

### RF-17 - Listar municipios ativos

O sistema deve carregar municipios ativos para selecao em formularios e filtros.

### RF-18 - Listar bairros por municipio

O sistema deve carregar dinamicamente os bairros ativos do municipio selecionado.

### RF-19 - Cadastrar bairro

O sistema deve permitir cadastro assicrono de bairro vinculado a municipio.

### RF-20 - Editar bairro

O sistema deve permitir edicao assicrona de bairro ja existente.

## Requisitos de gestao de usuarios

### RF-21 - Listar usuarios

O sistema deve permitir ao perfil `admin` listar usuarios com filtro por nome.

### RF-22 - Cadastrar usuario

O sistema deve permitir ao perfil `admin` criar usuarios com perfil e status definidos.

### RF-23 - Editar usuario

O sistema deve permitir ao perfil `admin` atualizar dados cadastrais de usuarios.

### RF-24 - Alterar senha de outro usuario

O sistema deve permitir ao perfil `admin` alterar a senha de outro usuario.

### RF-25 - Alterar propria senha

O sistema deve permitir a qualquer usuario autenticado (`admin`, `gestor`, `operador`) alterar a propria senha pela rota `/minha-senha`, com validacao da senha atual.

### RF-26 - Ativar e inativar usuario

O sistema deve permitir ao perfil `admin` alternar o status de usuarios.

## Requisitos de auditoria e relatorio

### RF-27 - Consultar historico de operacoes

O sistema deve permitir aos perfis `admin` e `gestor` consultar a trilha de auditoria.

### RF-28 - Filtrar historico

O sistema deve permitir filtrar historico por nome do usuario e acao.

### RF-29 - Gerar relatorio tecnico de hidrantes

O sistema deve permitir aos perfis `admin` e `gestor` gerar relatorio filtrado de hidrantes.

### RF-30 - Imprimir relatorio

O sistema deve permitir pre-visualizacao e impressao do relatorio em HTML.

## Requisitos explicitamente nao implementados

- recuperacao de senha por e-mail;
- API publica de terceiros;
- exportacao PDF nativa;
- seed automatico de municipios e bairros;
- modulo operacional de GeoJSON.
