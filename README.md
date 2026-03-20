# Sistema de Gestão de Hidrantes - MVC PHP

https://github.com/PaulaFernanda2785/hidrantes-system/blob/main/index.php

## Como usar

1. Copie `.env.example` para `.env`.
2. Ajuste as credenciais do banco.
3. Execute `composer dump-autoload`.
4. Importe `database/schema/schema.sql` no MySQL/MariaDB.
5. Configure o servidor para apontar para `public/` ou use o `index.php` da raiz.

## Estrutura principal implementada

- Autenticação por matrícula funcional e senha hash
- Painel com métricas básicas e dados de mapa
- Hidrantes: listagem e cadastro funcional
- Usuários: listagem e cadastro funcional
- Relatórios: listagem filtrável
- Histórico: listagem filtrável
- Endpoints JSON para mapa e bairros por município

## Limitações atuais

- Não inclui edição, exclusão e paginação real
- Não inclui CSRF aplicado em todos os formulários
- Não inclui mapa visual final com Leaflet já montado
- Não inclui tratamento completo de exceções por classe
