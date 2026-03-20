<h1><?= e($title) ?></h1>
<section class="card">
    <form method="POST" action="/usuarios/salvar" class="form-grid cols-2">
        <label class="col-span-2">Nome<input type="text" name="nome" required></label>
        <label>Matrícula funcional<input type="text" name="matricula_funcional" required></label>
        <label>Senha<input type="password" name="senha" required></label>
        <label>Perfil
            <select name="perfil" required>
                <option value="admin">admin</option>
                <option value="gestor">gestor</option>
                <option value="operador">operador</option>
            </select>
        </label>
        <label>Status
            <select name="status" required>
                <option value="ativo">ativo</option>
                <option value="inativo">inativo</option>
            </select>
        </label>
        <div class="col-span-2 actions-inline">
            <button type="submit">Salvar</button>
            <a class="btn-secondary" href="/usuarios">Cancelar</a>
        </div>
    </form>
</section>
