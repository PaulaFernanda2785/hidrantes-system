# Checklist Final de Publicacao

- [ ] Pasta `hidrantes_app` enviada fora do `public_html`
- [ ] Conteudo de `public_html` enviado para a hospedagem
- [ ] Arquivo `.env` configurado com os dados reais do servidor
- [ ] Banco importado
- [ ] Permissao de escrita conferida em `storage/uploads/hidrantes`
- [ ] Permissao de escrita conferida em `storage/framework`
- [ ] Permissao de escrita conferida em `storage/exports`
- [ ] Login funcionando
- [ ] Cadastro de hidrante funcionando
- [ ] Upload de fotos funcionando
- [ ] Painel com mapa funcionando
- [ ] Manual do usuario abrindo
- [ ] Relatorio tecnico abrindo e imprimindo
- [ ] HTTPS ativo para geolocalizacao

## Itens da atualizacao de senha (25/03/2026) - Concluidos

- [x] Menu lateral atualizado com `Alterar senha` para `admin`, `gestor` e `operador`
- [x] Rota `GET /minha-senha` adicionada para autoatendimento de senha
- [x] Rota `POST /minha-senha` adicionada para salvar nova senha do proprio usuario
- [x] Fluxo de troca da propria senha com validacao de `senha_atual`
- [x] Bloqueio do uso de `/usuarios/{id}/senha` para alterar a propria senha (redireciona para `/minha-senha`)
- [x] Tela de senha ajustada para dois cenarios: propria senha e senha de outro usuario (admin)
- [x] Copia de deploy `deploy/hospedagem-compartilhada/hidrantes_app` sincronizada com os mesmos arquivos alterados
- [x] Documentacao tecnica atualizada no projeto principal
- [x] Documentacao tecnica atualizada na copia de deploy
- [x] Validacao de sintaxe PHP executada com sucesso nos arquivos alterados (principal e deploy)
