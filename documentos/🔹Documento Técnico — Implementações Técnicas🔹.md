# Documento Tecnico - Implementacoes Tecnicas

## Panorama

Esta base implementa um conjunto de mecanismos tecnicos relevantes para operacao institucional, seguranca basica e manutencao evolutiva.

## Bootstrap e infraestrutura

- Autoload interno via `spl_autoload_register`.
- Leitura de ambiente via arquivo `.env`.
- Configuracao central por helper `config()`.
- Tratamento global de excecoes e erros fatais.
- Headers de seguranca enviados em todas as respostas.

## Sessao e autenticacao

- `Session` com cookies `HttpOnly` e `SameSite=Lax`.
- `session_regenerate_id` no login.
- invalidacao da sessao quando fingerprint nao confere.
- timeout por inatividade configurado em `config/session.php`.
- controle de tentativas de login persistido em arquivo com `flock`.

## Protecao de entrada e saida

- validacao CSRF central para requisicoes de escrita.
- prepared statements com `PDO`.
- `htmlspecialchars` em helper `e()`.
- saneamento de texto e identificadores em services.
- rejeicao de caminho externo em `redirect()`.

## Persistencia

- schema unico em SQL.
- consultas complexas isoladas em repositories.
- paginacao padronizada com metadados (`current_page`, `last_page`, `total`, etc.).
- exclusao logica apenas para `hidrantes`.

## Upload e entrega de arquivos

- validacao de MIME, dimensoes, pixels e tamanho do arquivo.
- renomeacao aleatoria para fotos.
- armazenamento em `storage/uploads/hidrantes`.
- entrega de imagens via controller, com regex de nome e validacao de MIME.

## Entrega de assets

- CSS e JS servidos por `AssetController` a partir de `resources/assets`.
- validacao com `realpath`.
- cache curto (`max-age=3600`).

## Mapa e geolocalizacao

- Leaflet carregado por CDN.
- camadas OpenStreetMap, HOT e Esri.
- drawer detalhado por hidrante.
- geolocalizacao nativa do navegador.
- calculo client-side de distancia entre usuario e hidrantes.

## Relatorio e exportacao

- exportacao CSV com BOM UTF-8.
- saneamento de CSV contra quebras de linha e formula injection.
- relatorio HTML preparado para `window.print()`.
- paginas adicionais de fotos no relatorio quando houver anexos.

## Observabilidade e apoio a diagnostico

- `report_exception()` grava falhas no log do PHP.
- modo local pode gerar `storage/framework/hidrante_upload_debug.log` em erros de upload.

## Limitacoes tecnicas atuais

- sem testes automatizados;
- sem build pipeline frontend;
- sem jobs assicronos;
- sem versionamento incremental de schema;
- sem API externa formal.
