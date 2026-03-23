# Manual de Hospedagem Compartilhada

## Objetivo

Este manual explica como publicar o sistema de hidrantes em hospedagem compartilhada sem depender do Wamp.

## Estrutura do pacote preparado

O pacote de publicação foi organizado com esta estrutura:

- `hidrantes_app/`: aplicação principal fora da área pública.
- `public_html/`: arquivos públicos que devem ficar na pasta pública da hospedagem.
- `banco/`: arquivos SQL para importação do banco.

## Passo a passo

1. Faça backup do sistema atual no Wamp.
   Exporte o banco e preserve a pasta `storage/uploads/hidrantes`.

2. Envie a pasta `hidrantes_app` para fora do `public_html`.
   Exemplo: `/home/USUARIO/hidrantes_app`

3. Envie o conteúdo da pasta `public_html` para a pasta pública da hospedagem.
   Exemplo: `/home/USUARIO/public_html`

4. No painel da hospedagem, crie um banco MySQL e um usuário com permissão total nesse banco.

5. Importe o arquivo SQL do pacote.
   Use primeiro `banco/hidrantes_db.sql` se quiser levar os dados atuais.
   Se precisar apenas da estrutura base, use `database/schema/schema.sql`.

6. Edite o arquivo `.env` dentro de `hidrantes_app`.
   Ajuste os campos:
   - `APP_URL`
   - `DB_HOST`
   - `DB_PORT`
   - `DB_NAME`
   - `DB_USER`
   - `DB_PASS`

7. Confirme se a hospedagem possui:
   - PHP 8.3 ou compatível
   - `pdo_mysql`
   - `mbstring`
   - `fileinfo`
   - Apache com `mod_rewrite`

8. Garanta permissão de escrita nestas pastas:
   - `storage/uploads/hidrantes`
   - `storage/framework`
   - `storage/framework/security`
   - `storage/exports`

9. Se a hospedagem permitir `.user.ini`, mantenha o arquivo enviado em `public_html/.user.ini`.

10. Acesse o domínio e teste o sistema.

## Observações importantes

- Mantenha o nome da pasta privada como `hidrantes_app`, porque o `public_html/index.php` do pacote já foi preparado para essa estrutura.
- O manual do usuário do painel foi duplicado com nome simples: `documentos/manual-do-usuario.html`.
- O painel usa HTTPS para geolocalização funcionar corretamente em navegador.
- O mapa depende de acesso externo aos provedores do Leaflet/OpenStreetMap/Esri.

## Checklist rápida

- Banco criado
- Banco importado
- `.env` ajustado
- `hidrantes_app` fora do `public_html`
- conteúdo de `public_html` enviado
- permissões de escrita conferidas
- login testado
- cadastro de hidrante testado
- upload de foto testado
- painel testado
- relatório testado
- manual testado
