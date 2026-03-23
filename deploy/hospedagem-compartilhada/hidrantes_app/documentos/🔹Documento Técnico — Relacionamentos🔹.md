# Documento Tecnico - Relacionamentos

## Relacoes entre entidades

## 1. `municipios` -> `bairros`

Cardinalidade:

- um municipio possui muitos bairros;
- um bairro pertence a um unico municipio.

Implementacao:

- `bairros.municipio_id -> municipios.id`

Politica referencial:

- `ON UPDATE CASCADE`
- `ON DELETE RESTRICT`

## 2. `municipios` -> `hidrantes`

Cardinalidade:

- um municipio possui muitos hidrantes;
- um hidrante pertence a um unico municipio.

Implementacao:

- `hidrantes.municipio_id -> municipios.id`

Politica referencial:

- `ON UPDATE CASCADE`
- `ON DELETE RESTRICT`

## 3. `bairros` -> `hidrantes`

Cardinalidade:

- um bairro pode referenciar muitos hidrantes;
- um hidrante pode ou nao estar associado a um bairro.

Implementacao:

- `hidrantes.bairro_id -> bairros.id`

Politica referencial:

- `ON UPDATE CASCADE`
- `ON DELETE SET NULL`

## 4. `usuarios` -> `historico_usuario`

Cardinalidade:

- um usuario pode gerar muitos eventos de historico;
- cada evento pertence a um unico usuario executor.

Implementacao:

- `historico_usuario.usuario_id -> usuarios.id`

Politica referencial:

- `ON UPDATE CASCADE`
- `ON DELETE RESTRICT`

## 5. `usuarios` -> `hidrantes` (autoria)

O cadastro de hidrantes possui tres relacoes distintas com usuarios:

- `criado_por_usuario_id`
- `atualizado_por_usuario_id`
- `deleted_por_usuario_id`

Politica referencial:

- `ON UPDATE CASCADE`
- `ON DELETE SET NULL`

Isso preserva o registro do hidrante caso o usuario de referencia deixe de existir no futuro.

## Relacoes logicas adicionais fora da FK

- A regra de negocio exige que `bairro_id`, quando informado, pertença ao `municipio_id` do hidrante.
- A tela de historico resolve nomes contextuais de hidrantes, usuarios e bairros via `LEFT JOIN`, mas essa navegacao nao cria novas FKs alem das declaradas no schema.
