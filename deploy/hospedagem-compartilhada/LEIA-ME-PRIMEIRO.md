# Pacote Pronto Para Hospedagem Compartilhada

## Estrutura

- `hidrantes_app/`: suba esta pasta para fora do `public_html`.
- `public_html/`: envie o conteúdo desta pasta para a área pública da hospedagem.
- `banco/`: contém o SQL para importação.

## Ordem recomendada

1. Criar banco na hospedagem.
2. Importar `banco/hidrantes_db.sql`.
3. Enviar `hidrantes_app`.
4. Enviar conteúdo de `public_html`.
5. Ajustar `hidrantes_app/.env`.
6. Testar login, painel, hidrantes, fotos e relatórios.
