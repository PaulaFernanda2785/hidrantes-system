# Documento Tecnico - Modelagem

## Modelo conceitual atual

O sistema foi modelado com cinco entidades persistidas e relacoes diretas entre acesso, territorio e operacao.

## Entidades centrais

### Usuario

Representa o agente autenticado do sistema.

Atributos de negocio principais:

- nome;
- matricula funcional;
- senha hash;
- perfil;
- status.

Perfis validos:

- `admin`
- `gestor`
- `operador`

### Municipio

Representa a unidade administrativa de referencia do hidrante.

Atributos principais:

- nome;
- codigo IBGE opcional;
- UF;
- flag `ativo`.

### Bairro

Representa uma subdivisao territorial vinculada a um municipio.

Atributos principais:

- municipio de vinculacao;
- nome;
- codigo IBGE opcional;
- `geojson_referencia` opcional;
- flag `ativo`.

### Hidrante

Representa o cadastro tecnico operacional do ponto de combate a incendio.

Atributos principais:

- identificacao (`numero_hidrante`);
- equipe responsavel;
- area;
- existencia no local;
- tipo;
- acessibilidade;
- condicoes fisicas;
- resultado de teste;
- status operacional;
- localizacao territorial;
- coordenadas;
- fotos;
- autoria de criacao, atualizacao e exclusao logica.

Estados operacionais:

- `operante`
- `operante com restricao`
- `inoperante`

### HistoricoUsuario

Representa a trilha de auditoria de operacoes do sistema.

Atributos principais:

- data e hora da acao;
- usuario executor;
- snapshot do nome do usuario;
- acao;
- entidade afetada;
- referencia do registro;
- detalhes;
- IP de origem.

## Invariantes de modelagem

- `usuarios.matricula_funcional` e unico.
- `hidrantes.numero_hidrante` e unico.
- `bairros` nao podem repetir `nome` dentro do mesmo `municipio_id`.
- `hidrante.bairro_id`, quando informado, deve apontar para bairro do mesmo municipio na regra de negocio.
- `hidrantes` excluidos continuam reservando o `numero_hidrante`, pois a verificacao de duplicidade nao ignora `deleted_at`.

## Modelo logico resumido

```text
Usuario 1 --- N HistoricoUsuario
Usuario 1 --- N Hidrante (criado_por)
Usuario 1 --- N Hidrante (atualizado_por)
Usuario 1 --- N Hidrante (deleted_por)
Municipio 1 --- N Bairro
Municipio 1 --- N Hidrante
Bairro 1 --- N Hidrante
```

## Observacoes de modelagem

- O sistema nao usa models ricos em codigo; a modelagem fica refletida principalmente no schema SQL e nas validacoes dos services.
- `resultado_teste` nao e obrigatorio mesmo quando `teste_realizado = sim`; a regra implementada apenas exige que ele seja nulo quando `teste_realizado = nao`.
- `historico_usuario.acao` possui valores reservados no enum para `baixar csv` e `gerar relatorio`, mas esses eventos ainda nao sao gravados pela aplicacao atual.
