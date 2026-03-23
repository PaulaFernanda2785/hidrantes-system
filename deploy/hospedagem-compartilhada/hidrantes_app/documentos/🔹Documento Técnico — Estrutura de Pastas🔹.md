# Documento Tecnico - Estrutura de Pastas

## Visao geral

A estrutura do repositorio segue organizacao por responsabilidade tecnica. O objetivo e separar claramente codigo de aplicacao, configuracao, banco, assets, views, arquivos publicos e armazenamento operacional.

## Arvore principal

```text
hidrantes-system/
|-- app/
|   |-- Controllers/
|   |-- Core/
|   |-- Helpers/
|   |-- Middleware/
|   |-- Repositories/
|   |-- Services/
|   `-- Validators/
|-- config/
|-- database/
|   `-- schema/
|-- documentos/
|-- public/
|   |-- img/
|   `-- assets/        (presente no repositorio, mas nao e a origem principal servida pelas rotas)
|-- resources/
|   |-- assets/
|   |   |-- css/
|   |   `-- js/
|   `-- views/
|-- routes/
|-- storage/
|   |-- framework/
|   `-- uploads/
|-- vendor/
|-- .env
|-- .env.example
|-- composer.json
`-- README.md
```

## Detalhamento por diretorio

### `app`

Contem a logica de aplicacao:

- `Core`: classes estruturais do microframework interno.
- `Controllers`: coordenacao dos casos de uso.
- `Services`: regras de negocio.
- `Repositories`: persistencia SQL.
- `Middleware`: autenticacao e autorizacao.
- `Helpers`: funcoes globais.
- `Validators`: excecao de validacao.

### `config`

Arquivos de configuracao carregados por helper:

- `app.php`
- `database.php`
- `session.php`

### `database/schema`

Contem o schema SQL unico do projeto:

- `schema.sql`

### `public`

Area exposta pelo servidor web:

- `index.php` de entrada.
- imagens estaticas institucionais.
- assets legados ou auxiliares presentes no repositorio.

### `resources`

Origem principal de interface:

- `views`: templates PHP.
- `assets/css`: estilos por escopo.
- `assets/js`: scripts de comportamento e integracao com API interna.

### `routes`

Separacao entre:

- `web.php`: paginas e formularios.
- `api.php`: endpoints JSON internos.

### `storage`

Dados operacionais gerados em runtime:

- `uploads/hidrantes`: imagens anexadas.
- `framework/security`: estado do limitador de login.
- `framework/*.log`: logs auxiliares em ambiente local.

## Observacoes importantes

- A entrega de CSS e JS em execucao passa por `AssetController`, que le arquivos de `resources/assets`.
- O diretorio `documentos` faz parte da base de engenharia e nao participa do runtime da aplicacao.
- `vendor` existe no repositorio, mas o carregamento de classes em runtime e feito por autoload proprio registrado em `public/index.php`.
