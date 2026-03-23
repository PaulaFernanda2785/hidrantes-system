# Documento Tecnico - Requisitos Nao Funcionais

## RNF-01 - Seguranca de autenticacao

O sistema deve armazenar senha apenas em formato hash e validar autenticacao em servidor.

## RNF-02 - Seguranca de sessao

O sistema deve utilizar cookie `HttpOnly`, regeneracao de sessao no login e expiracao por inatividade.

## RNF-03 - Protecao CSRF

Operacoes de escrita devem exigir token CSRF valido.

## RNF-04 - Controle de acesso

Permissoes devem ser validadas no backend, independentemente da interface ocultar ou nao determinada acao.

## RNF-05 - Integridade referencial

O banco deve manter consistencia entre entidades por meio de chaves estrangeiras, unicidade e constraints declarativas.

## RNF-06 - Desempenho minimo de consulta

Listagens principais devem operar com paginacao e indices adequados para filtros recorrentes.

## RNF-07 - Rastreabilidade

Operacoes criticas de autenticacao e manutencao de cadastros devem gerar historico persistido.

## RNF-08 - Confiabilidade de upload

Arquivos de imagem devem ser validados por tipo, tamanho e dimensao antes do armazenamento.

## RNF-09 - Manutenibilidade

O codigo deve permanecer dividido em controllers, services, repositories e views, evitando SQL em controller e regra de negocio espalhada na view.

## RNF-10 - Portabilidade de execucao

O sistema deve operar em ambiente PHP com servidor web tradicional, sem exigir infraestrutura de containers, filas ou servicos externos obrigatorios.

## RNF-11 - Usabilidade operacional

As telas principais devem ser responsivas e utilizaveis em desktop e dispositivos moveis.

## RNF-12 - Degradacao controlada

Se a biblioteca de mapa nao carregar ou se a geolocalizacao falhar, a interface deve continuar funcional com mensagens de estado.

## RNF-13 - Observabilidade basica

Falhas devem ser registradas no log do PHP e erros fatais devem retornar mensagem generica ao usuario final.

## RNF-14 - Baixo acoplamento externo

O backend deve manter numero reduzido de dependencias externas e funcionar majoritariamente com recursos nativos do PHP.

## RNF-15 - Limitacoes reconhecidas

O estado atual nao atende integralmente aos seguintes aspectos desejaveis:

- teste automatizado;
- migracoes versionadas;
- auditoria completa de exportacao/impressao;
- governanca automatizada de dados referenciais territoriais.
