# Documento Tecnico - Chaves Primarias e Estrangeiras

## Chaves primarias

Todas as tabelas usam chave primaria simples com `BIGINT UNSIGNED AUTO_INCREMENT`.

| Tabela | Chave primaria |
| --- | --- |
| `usuarios` | `id` |
| `municipios` | `id` |
| `bairros` | `id` |
| `hidrantes` | `id` |
| `historico_usuario` | `id` |

## Chaves estrangeiras

| Tabela origem | Coluna | Referencia | Politica de update | Politica de delete |
| --- | --- | --- | --- | --- |
| `bairros` | `municipio_id` | `municipios.id` | `CASCADE` | `RESTRICT` |
| `hidrantes` | `municipio_id` | `municipios.id` | `CASCADE` | `RESTRICT` |
| `hidrantes` | `bairro_id` | `bairros.id` | `CASCADE` | `SET NULL` |
| `hidrantes` | `criado_por_usuario_id` | `usuarios.id` | `CASCADE` | `SET NULL` |
| `hidrantes` | `atualizado_por_usuario_id` | `usuarios.id` | `CASCADE` | `SET NULL` |
| `hidrantes` | `deleted_por_usuario_id` | `usuarios.id` | `CASCADE` | `SET NULL` |
| `historico_usuario` | `usuario_id` | `usuarios.id` | `CASCADE` | `RESTRICT` |

## Implicacoes tecnicas

- Um municipio nao pode ser removido enquanto houver bairros ou hidrantes vinculados.
- Um bairro pode ser removido da referencia de hidrantes por `SET NULL`.
- O historico exige permanencia do usuario executor.
- A autoria de hidrantes foi modelada para suportar nulidade caso o usuario relacionado deixe de existir.

## Observacao de negocio

Apesar das FKs garantirem integridade estrutural, a coerencia `bairro -> municipio` em hidrantes e reforcada na camada de servico, pois depende de validacao combinada de dois campos de negocio.
