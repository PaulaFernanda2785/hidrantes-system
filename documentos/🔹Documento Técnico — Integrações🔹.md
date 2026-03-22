# Documento Tecnico - Integracoes

## Integracoes efetivamente utilizadas

O sistema possui integracoes externas e internas de baixa complexidade, voltadas a geovisualizacao, geolocalizacao, navegacao assistida e comunicacao assicrona entre frontend e backend.

## Integracoes externas

### Leaflet

Uso:

- renderizacao do mapa operacional;
- exibicao de marcadores por status;
- interacao com zoom, arraste e controle de camadas.

Forma de carga:

- CSS e JS carregados por CDN em `layout/auth` e `PainelController`.

### OpenStreetMap

Uso:

- camada cartografica principal do mapa;
- iframe de pre-visualizacao de localizacao em formularios e modais.

### HOT OpenStreetMap

Uso:

- camada operacional alternativa no painel.

### Esri World Imagery

Uso:

- camada satelite no painel operacional.

### Google Maps

Uso:

- abertura de rota para o hidrante selecionado.

Observacao:

- a URL e montada no frontend com base em latitude e longitude validadas.

### API de Geolocalizacao do navegador

Uso:

- obter a localizacao atual do usuario;
- preencher coordenadas no formulario de hidrante;
- calcular proximidade no painel.

### Captura de camera e upload do navegador

Uso:

- anexar fotos diretamente do dispositivo, inclusive com `capture="environment"` em dispositivos moveis.

## Integracoes internas

### API interna de bairros

Rotas:

- `GET /api/bairros/municipio/{id}`
- `POST /api/bairros`
- `POST /api/bairros/{id}`

Uso:

- carregar bairros dinamicamente no filtro e no formulario;
- cadastrar bairro sem recarregar a pagina;
- editar bairro sem recarregar a pagina.

### API interna de mapa

Rota:

- `GET /api/hidrantes/mapa`

Uso:

- disponibilizar pontos georreferenciados autenticados em JSON.

Observacao:

- a tela de login nao usa essa rota; ela recebe os pontos ja renderizados no HTML.

### Streaming de imagens

Rotas:

- `GET /uploads/hidrantes/{filename}` autenticada
- `GET /painel/fotos/hidrantes/{filename}` publica

Uso:

- proteger entrega de fotos na area interna;
- permitir exibicao de fotos no painel publico da tela de login.

## Integracoes previstas no modelo de dados, mas nao operacionais

- O campo `bairros.geojson_referencia` indica preparacao para material geoespacial, mas nao existe pipeline atual de importacao ou consulta GeoJSON.
- Nao ha integracao com e-mail, SMS, sistemas legados ou webservices governamentais.
