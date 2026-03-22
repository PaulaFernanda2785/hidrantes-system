# Documento Tecnico - Manual do Usuario - Resumo

## Referencia

- Sistema: Sistema de Gestao de Hidrantes
- Instituicao: Corpo de Bombeiros Militar do Estado do Para / CEDEC-PA
- Data de referencia: 22/03/2026
- Objetivo: orientar o uso cotidiano do sistema de forma rapida e objetiva

## 1. Visao geral

O sistema permite consultar, cadastrar, atualizar e acompanhar hidrantes com apoio de mapa, filtros, fotos, relatorios e historico de operacoes. O acesso ocorre por perfil de usuario.

## 2. Perfis de usuario

- `admin`: acessa painel, hidrantes, usuarios, relatorios e historico.
- `gestor`: acessa painel, hidrantes, relatorios e historico.
- `operador`: acessa painel, consulta hidrantes e pode editar hidrantes existentes, mas nao cria, nao exclui e nao altera o numero do hidrante.

## 3. Login e sessao

- O acesso e feito por matricula funcional e senha.
- A conta precisa estar ativa.
- Depois de 5 tentativas falhas, o login pode ser bloqueado temporariamente por cerca de 15 minutos.
- A sessao pode expirar por inatividade.
- Para sair corretamente, use o botao `Sair`.

## 4. Painel operacional

No painel o usuario encontra:

- total de hidrantes;
- total por status operacional;
- mapa interativo;
- localizacao atual do usuario, quando permitida;
- lista de hidrantes mais proximos;
- detalhe tecnico do hidrante selecionado.

## 5. Consulta de hidrantes

Na tela `Hidrantes`, o usuario pode:

- listar registros paginados;
- filtrar por busca textual;
- filtrar por status operacional;
- filtrar por municipio;
- filtrar por bairro;
- abrir detalhe em modal;
- exportar CSV da listagem filtrada.

## 6. Cadastro e edicao de hidrantes

O formulario de hidrante possui blocos para:

- identificacao e operacao;
- condicoes fisicas;
- teste e desempenho;
- localizacao e referencia;
- registro fotografico.

Cuidados importantes:

- o numero do hidrante deve ser unico;
- o bairro deve pertencer ao municipio selecionado;
- coordenadas invalidas nao devem ser salvas;
- o resultado do teste deve ser coerente com a informacao de teste realizado.

## 7. Coordenadas, bairros e fotos

- As coordenadas podem ser informadas manualmente ou pela localizacao atual do dispositivo.
- O sistema oferece pre-visualizacao do ponto no mapa.
- O bairro pode ser cadastrado ou editado diretamente no formulario, desde que o municipio esteja selecionado.
- Sao aceitas ate 3 fotos por hidrante.

## 8. Gestao de usuarios

Exclusiva do perfil `admin`.

Funcoes principais:

- cadastrar usuario;
- editar usuario;
- alterar senha;
- ativar ou inativar conta;
- filtrar usuarios por nome.

## 9. Relatorios

Disponiveis para `admin` e `gestor`.

O modulo permite:

- aplicar filtros;
- gerar relatorio em tela;
- abrir pre-visualizacao para impressao;
- imprimir pelo navegador.

## 10. Historico

Disponivel para `admin` e `gestor`.

Permite:

- filtrar por nome do usuario;
- filtrar por acao;
- consultar detalhes de eventos relevantes, como login, edicao, cadastro e alteracao de status.

## 11. Problemas comuns

- Login negado: revisar matricula, senha e status da conta.
- Bairro nao listado: selecionar o municipio antes.
- Mapa sem ponto: revisar latitude e longitude.
- Geolocalizacao falhou: habilitar permissao do navegador e usar HTTPS ou localhost.
- Upload rejeitado: revisar formato, tamanho e quantidade de imagens.

## 12. Boas praticas

- Conferir o perfil autenticado antes de iniciar operacoes.
- Revisar endereco, municipio, bairro e coordenadas antes de salvar.
- Usar fotos claras e atualizadas.
- Encerrar a sessao ao finalizar o uso.
- Preferir editar o hidrante existente em vez de criar registro duplicado.
