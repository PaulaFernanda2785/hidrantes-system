# Sistema de Gestao de Hidrantes - MVC PHP

## Como usar

1. Copie `.env.example` para `.env`.
2. Ajuste as credenciais do banco.
3. Execute `composer dump-autoload`.
4. Importe `database/schema/schema.sql` no MySQL/MariaDB.
5. Configure o `DocumentRoot` ou o `VirtualHost` para apontar para `public/`.
6. Acesse o sistema com a matricula `000000` e a senha `admin123`.

## Estrutura principal

- Autenticacao por matricula funcional e senha hash.
- Painel com metricas basicas e dados de mapa.
- Hidrantes com listagem, cadastro, edicao e exclusao logica.
- Usuarios com listagem e cadastro.
- Relatorios filtraveis.
- Historico de acoes.
- Endpoints JSON para mapa e bairros por municipio.
- Protecao CSRF aplicada nos formularios `POST`.

## Observacoes

- As fotos dos hidrantes ficam em `storage/uploads/hidrantes` e sao servidas por rota autenticada.
- O projeto ainda nao possui suite automatizada de testes.
- Troque a senha inicial do usuario administrador apos a instalacao.
