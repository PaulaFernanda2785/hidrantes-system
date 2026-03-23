# Documento Tecnico - Ajustes e Otimizacoes

## Ajustes tecnicos incorporados na base atual

### Seguranca

- protecao CSRF centralizada no `Router`;
- fingerprint de sessao baseada em `user-agent`;
- timeout por inatividade;
- headers de seguranca HTTP;
- validacao de caminho em `redirect()`;
- validacao de MIME e nome ao servir fotos;
- prepared statements em repositories.

### Consistencia de negocio

- validacao server-side de enums de hidrante;
- validacao de pertenca de bairro ao municipio;
- politica minima de senha;
- impedimento de autoalteracao de perfil/status pelo proprio usuario;
- restricao do operador ao editar numero do hidrante.

### Performance e consulta

- paginacao em hidrantes, usuarios e historico;
- indices especificos para status, municipio, bairro e data;
- agregacoes SQL para o painel;
- uso de filtros combinados em repository.

### Arquivos e upload

- limitacao a 3 fotos por hidrante;
- restricao de extensoes aceitas;
- validacao de resolucao e pixels;
- renomeacao aleatoria de arquivos;
- armazenamento em `storage`.

### Experiencia operacional

- carga dinamica de bairros por municipio;
- pre-visualizacao de mapa no formulario;
- captura por camera em dispositivos moveis;
- detalhe rapido por modal/drawer;
- relatorio com modo de impressao.

## Ajustes ainda recomendados para proximas iteracoes

- registrar auditoria de exportacao CSV e emissao de relatorio;
- criar migracoes versionadas;
- automatizar seeds territoriais;
- criar suite de testes;
- separar `HidranteController` em camadas HTML/API/arquivo para reduzir acoplamento;
- revisar a exposicao publica do painel na tela de login, caso a politica institucional exija sigilo maior.
